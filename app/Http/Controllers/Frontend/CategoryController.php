<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Helper;
use App\Models\Story;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Chapter\ChapterRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
        protected ChapterRepositoryInterface $chapterRepository
    )
    {

    }

    public function index(Request $request, $slug) {
        $category = $this->categoryRepository->getCategoryBySlug($slug);

        // Kiểm tra nếu không tìm thấy category
        if (!$category) {
            abort(404, 'Thể loại không tồn tại');
        }
        if (!$category->stories) {
            $stories = collect();
        } else {
            $stories = $category->stories->where('status', '=', Story::STATUS_ACTIVE);
        }

        // SEO for category page
        $title = "{$category->name} - Akay Truyện";
        $description = Str::limit(strip_tags($category->desc), 160) ?: "Đọc truyện {$category->name} online, truyện hay. Akay Truyện luôn tổng hợp và cập nhật các chương truyện một cách nhanh nhất.";
        $keywords = str_replace('-', ' ', $category->slug) . ', doc truyen, doc truyen online, truyen hay, truyen chu';
        
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOMeta::setKeywords($keywords);
        SEOTools::setCanonical(url()->current());

        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description);
        OpenGraph::setUrl(url()->current());
        OpenGraph::addProperty('type', 'website');
        OpenGraph::addImage(asset('images/logo/Logoakay.png'));

        TwitterCard::setTitle($title);
        TwitterCard::setDescription($description);
        TwitterCard::setSite('@AkayTruyen');
        TwitterCard::addImage(asset('images/logo/Logoakay.png'));

        // $storiesIds = [];
        // if (count($stories) > 0) {
        //     foreach ($stories as $story) {
        //         $storiesIds[] = $story->id;
        //     }
        // }

        // $chapterLast = [];

        // if ($storiesIds) {
        //     $chapterLast = $this->chapterRepository->getChapterLast($storiesIds);
        //     $stories->map(function ($story) use ($chapterLast) {
        //         foreach ($chapterLast as $chapter) {
        //             if ($story->id == $chapter->story_id) {
        //                 return $story->chapter_last = $chapter;
        //             }
        //         }
        //     });
        // }

        return view('Frontend.category', compact('category', 'stories'));
    }
}
