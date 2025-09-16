<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Helper;
use App\Models\Chapter;
use App\Models\Comment;
use App\Repositories\Chapter\ChapterRepositoryInterface;
use App\Repositories\Story\StoryRepositoryInterface;
use App\Repositories\Comment\CommentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;

class ChapterController extends Controller
{
    public function __construct(
        protected StoryRepositoryInterface $storyRepository,
        protected ChapterRepositoryInterface $chapterRepository,
        protected CommentRepositoryInterface $commentRepository
    ) {}

    public function index(Request $request, $slugStory, $slugChapter)
    {

        $story = $this->storyRepository->getCachedStoryDetail($slugStory);

        if (!$story) {
            return abort(404);
        }

        $chapter = $this->chapterRepository->getCachedChapterData($story->id, $slugChapter);
        $chapterLast = $chapter->chapterLast ?? $this->chapterRepository->getChapterLastSingle($story->id);

        if (!$chapter) {
            return abort(404, 'Không tồn tại chương truyện này!');
        }


        $chapterBefore = $chapter->chapterBefore ?? null;
        $chapterAfter = $chapter->chapterAfter ?? null;

        // SEO for chapter page
        $title = "Chương {$chapter->chapter}: {$chapter->name} - {$story->name} - Akay Truyện";
        $description = Str::limit(strip_tags($story->desc), 160);
        $keywords = "chương {$chapter->chapter}, {$story->name}, doc truyen, doc truyen online, truyen hay, truyen chu";
        
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOMeta::setKeywords($keywords);
        SEOTools::setCanonical(url()->current());

        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description);
        OpenGraph::setUrl(url()->current());
        OpenGraph::addProperty('type', 'article');
        OpenGraph::addImage(Helper::getStoryImageUrl($story->image));
        OpenGraph::addProperty('article:author', $story->author?->name ?? 'Chưa xác định');
        OpenGraph::addProperty('article:published_time', $chapter->created_at->toAtomString());
        OpenGraph::addProperty('article:section', $story->name);

        TwitterCard::setTitle($title);
        TwitterCard::setDescription($description);
        TwitterCard::setSite('@AkayTruyen');
        TwitterCard::addImage(Helper::getStoryImageUrl($story->image));

        $cleanContent = strip_tags($chapter->content);
        $words = preg_split('/\s+/u', trim($cleanContent));
        $chapter->word_count = count($words);

        $ip = $request->ip();
        $sessionKey = "chapter_view_{$chapter->id}_{$ip}";
        if (!session()->has($sessionKey)) {
            $chapter->increment('views');
            session([$sessionKey => true]);
        }

        $breadcrumbEndpoint = 'Chương ' . $chapter->chapter;

        $pinnedComments = $this->commentRepository->getCachedChapterComments($chapter->id, true, 10);
        $regularComments = $this->commentRepository->getChapterCommentsPaginated($chapter->id, false, 10);

        if ($request->ajax()) {
            if ($request->type === 'comments') {
                $showPinned = $request->page == 1;
                return response()->json([
                    'html' => view('components.comments-list', [
                        'pinnedComments' => $showPinned ? $pinnedComments : collect([]),
                        'regularComments' => $regularComments
                    ])->render(),
                    'hasMore' => false
                ]);
            }

            return response()->json([
                'html' => view('components.chapter-items', compact('chapters'))->render()
            ]);
        }

        return view('Frontend.chapter', compact(
            'story',
            'chapter',
            'slugChapter',
            'chapterLast',
            'breadcrumbEndpoint',
            'chapterBefore',
            'chapterAfter',
            'pinnedComments',
            'regularComments',
            'slugStory'
        ));
    }

    public function getChapters(Request $request)
    {
        $res = ['success' => false];

        $listChapter = $this->chapterRepository->getListChapterByStoryId($request->input('story_id'));

        $res['chapters'] = $listChapter;
        $res['success'] = true;

        return response()->json($res);
    }


    public function saveChapter(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để lưu chương.']);
            }

            if (!$request->has('chapter_id')) {
                return response()->json(['success' => false, 'message' => 'Thiếu ID chương.']);
            }

            $chapterId = $request->chapter_id;
            $user = auth()->user();

            // Kiểm tra chapter có tồn tại không
            $chapterExists = DB::table('chapters')->where('id', $chapterId)->exists();
            if (!$chapterExists) {
                return response()->json(['success' => false, 'message' => 'Chương không tồn tại.']);
            }

            // Kiểm tra xem chương đã được lưu chưa
            if (!$user->savedChapters()->where('chapter_id', $chapterId)->exists()) {
                $user->savedChapters()->attach($chapterId);
            }

            return response()->json(['success' => true, 'message' => 'Chương đã được lưu!']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống. Vui lòng thử lại sau!',
                'error' => $e->getMessage() // Hiển thị lỗi để debug (chỉ nên bật khi phát triển)
            ], 500);
        }
    }

    public function removeChapter(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để thực hiện thao tác này.']);
        }

        $chapterId = $request->input('chapter_id');
        $user->savedChapters()->detach($chapterId);

        return response()->json(['success' => true]);
    }
}
