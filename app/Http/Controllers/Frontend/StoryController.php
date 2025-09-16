<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Helper;
use App\Models\Rating;
use App\Models\Chapter;
use App\Models\Story;
use App\Models\User;
use App\Models\Donate;
use App\Repositories\Chapter\ChapterRepositoryInterface;
use App\Repositories\Rating\RatingRepositoryInterface;
use App\Repositories\Story\StoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;

class StoryController extends Controller
{
    public function __construct(
        protected StoryRepositoryInterface $storyRepository,
        protected ChapterRepositoryInterface $chapterRepository,
        protected RatingRepositoryInterface $ratingRepository
    ) {}

    public function index(Request $request, $slug)
    {
        // Use cached optimized story detail
        $story = $this->storyRepository->getCachedStoryDetail($slug);
        if (!$story) {
            abort(404, 'Truyện không tồn tại');
        }

        // Get pagination settings
        $isOldFirst = filter_var($request->old_first, FILTER_VALIDATE_BOOLEAN);
        $page = $request->get('page', 1);

        // Use cached chapters with pagination
        $chapters = $this->storyRepository->getCachedStoryChapters($story->id, $page, $isOldFirst);
        $chaptersNew = $this->chapterRepository->getChaptersNewByStoryId($story->id);


        // Cache ratings data
        $ratingsDay = $this->ratingRepository->getRatingByType(Rating::TYPE_DAY);
        $arrStoryIdsRatingsDay = $this->getStoryIds(json_decode($ratingsDay->value ?? '', true)) ?? [];
        $storiesDay = $this->ratingRepository->getStories($arrStoryIdsRatingsDay) ?? [];

        $ratingsMonth = $this->ratingRepository->getRatingByType(Rating::TYPE_MONTH);
        $arrStoryIdsRatingsMonth = $this->getStoryIds(json_decode($ratingsMonth->value ?? '', true)) ?? [];
        $storiesMonth = $this->ratingRepository->getStories($arrStoryIdsRatingsMonth) ?? [];

        $ratingsAllTime = $this->ratingRepository->getRatingByType(Rating::TYPE_ALL_TIME);
        $arrStoryIdsRatingsAllTime = $this->getStoryIds(json_decode($ratingsAllTime->value ?? '', true)) ?? [];
        $storiesAllTime = $this->ratingRepository->getStories($arrStoryIdsRatingsAllTime) ?? [];

        // SEO for story page
        $title = "{$story->name} - Akay Truyện";
        $description = Str::limit(strip_tags($story->desc), 160);
        $keywords = str_replace('-', ' ', $story->slug) . ', doc truyen, doc truyen online, truyen hay, truyen chu';
        
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOMeta::setKeywords($keywords);
        SEOTools::setCanonical(url()->current());

        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description);
        OpenGraph::setUrl(url()->current());
        OpenGraph::addProperty('type', 'book');
        OpenGraph::addImage(Helper::getStoryImageUrl($story->image));
        OpenGraph::addProperty('book:author', $story->author?->name ?? 'Chưa xác định');
        OpenGraph::addProperty('book:release_date', $story->created_at->toAtomString());

        TwitterCard::setTitle($title);
        TwitterCard::setDescription($description);
        TwitterCard::setSite('@AkayTruyen');
        TwitterCard::addImage(Helper::getStoryImageUrl($story->image));

        // Use cached story stats instead of individual queries
        $storyStats = $this->storyRepository->getCachedStoryStats($story->id);
        $totalChapters = $storyStats['total_chapters'];
        $totalViews = $storyStats['total_views'];
        $storyViews = $totalViews; // same as totalViews

        $isOldFirst = filter_var($request->old_first, FILTER_VALIDATE_BOOLEAN);
        $orderDirection = $isOldFirst ? 'asc' : 'desc';
        // Helper::setSEO($objectSEO);

        // Use cached chapter ranges to avoid min/max queries
        $ranges = $this->storyRepository->getCachedChapterRanges($story->id);

        // Kiểm tra nếu cần đảo ngược thứ tự chương
        $isOldFirst = $request->input('old_first', 0);
        if ($isOldFirst && !empty($ranges)) {
            $ranges = array_reverse($ranges);
        }

        // Lấy thông tin donate cho truyện này
        $donates = $story->donates()->orderBy('created_at', 'desc')->get();

        return view('Frontend.story', compact('story', 'ranges', 'chapters', 'chaptersNew', 'slug', 'ratingsDay', 'ratingsMonth', 'ratingsAllTime', 'storiesDay', 'storiesMonth', 'storiesAllTime', 'totalChapters', 'totalViews', 'storyViews', 'donates'));
    }

    protected function getStoryIds($ratings)
    {
        $result = [];

        if ($ratings) {
            foreach ($ratings as $rating) {
                $result[] = $rating['id'];
            }
        }

        return $result;
    }

    public function followChaptersCount(Request $request)
    {
        // dd($request->input());
        $stories = $this->storyRepository->getStoriesWithChaptersCount($request->input('value'));
        if ($request->input('value')[1] != 999999999) {
            $title = $request->input('value')[0] . ' - ' . $request->input('value')[1] . ' chương';
        } else {
            $title = 'Trên ' . $request->input('value')[0] . ' chương';
        }

        // SEO for chapter count page
        $seoTitle = "Truyện {$title} - Akay Truyện";
        $description = "Danh sách truyện có {$title}. Tìm thấy " . count($stories) . " truyện phù hợp.";
        
        SEOTools::setTitle($seoTitle);
        SEOTools::setDescription($description);
        SEOMeta::setKeywords("truyen {$title}, doc truyen, doc truyen online, truyen hay, truyen chu");
        SEOTools::setCanonical(url()->current());

        OpenGraph::setTitle($seoTitle);
        OpenGraph::setDescription($description);
        OpenGraph::setUrl(url()->current());
        OpenGraph::addProperty('type', 'website');
        OpenGraph::addImage(asset('images/logo/Logoakay.png'));

        TwitterCard::setTitle($seoTitle);
        TwitterCard::setDescription($description);
        TwitterCard::setSite('@AkayTruyen');
        TwitterCard::addImage(asset('images/logo/Logoakay.png'));

        $ratingsDay = $this->ratingRepository->getRatingByType(Rating::TYPE_DAY);
        $arrStoryIdsRatingsDay = $this->getStoryIds(json_decode($ratingsDay->value, true));
        $storiesDay = $this->ratingRepository->getStories($arrStoryIdsRatingsDay);

        $ratingsMonth = $this->ratingRepository->getRatingByType(Rating::TYPE_MONTH);
        $arrStoryIdsRatingsMonth = $this->getStoryIds(json_decode($ratingsMonth->value, true));
        $storiesMonth = $this->ratingRepository->getStories($arrStoryIdsRatingsMonth);

        $ratingsAllTime = $this->ratingRepository->getRatingByType(Rating::TYPE_ALL_TIME);
        $arrStoryIdsRatingsAllTime = $this->getStoryIds(json_decode($ratingsAllTime->value, true));
        $storiesAllTime = $this->ratingRepository->getStories($arrStoryIdsRatingsAllTime);

        return view('Frontend.follow_chapter_count', compact('title', 'stories', 'ratingsDay', 'ratingsMonth', 'ratingsAllTime', 'storiesDay', 'storiesMonth', 'storiesAllTime'));
    }

    public function toggleVip(Request $request, Story $story)
    {
        $user = auth()->user();
        
        if (!$user->can('sua_truyen')) {
            return response()->json(['message' => 'Bạn không có quyền thực hiện thao tác này'], 403);
        }
        
        $isVip = $request->input('is_vip', 0);
        $story->update(['is_vip' => $isVip]);
        
        $message = $isVip ? 'Đã bật chế độ VIP cho truyện' : 'Đã tắt chế độ VIP cho truyện';
        
        return response()->json(['message' => $message]);
    }
}
