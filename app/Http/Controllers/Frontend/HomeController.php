<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Helpers\Helper;
use App\Models\Chapter;
use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Chapter\ChapterRepositoryInterface;
use App\Repositories\Story\StoryRepositoryInterface;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;

use App\Models\Donation;

class HomeController extends Controller
{
    public function __construct(
        protected StoryRepositoryInterface $storyRepository,
        protected ChapterRepositoryInterface $chapterRepository,
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function index(Request $request)
    {
        // Load SEO settings from database for home page
        $seoSetting = \App\Models\SeoSetting::getByPageKey('home');
        
        if ($seoSetting) {
            SEOTools::setTitle($seoSetting->title);
            SEOTools::setDescription($seoSetting->description);
            SEOMeta::setKeywords($seoSetting->keywords);
            SEOTools::setCanonical(url()->current());

            OpenGraph::setTitle($seoSetting->title);
            OpenGraph::setDescription($seoSetting->description);
            OpenGraph::setUrl(url()->current());
            OpenGraph::addProperty('type', 'website');
            if ($seoSetting->thumbnail) {
                OpenGraph::addImage($seoSetting->thumbnail_url);
            }

            TwitterCard::setTitle($seoSetting->title);
            TwitterCard::setDescription($seoSetting->description);
            TwitterCard::setSite('@AkayTruyen');
            if ($seoSetting->thumbnail) {
                TwitterCard::addImage($seoSetting->thumbnail_url);
            }
        } else {
            // Fallback SEO
            SEOTools::setTitle('Trang chủ - Akay Truyện');
            SEOTools::setDescription('Đọc truyện miễn phí với giao diện đẹp, cập nhật nhanh nhất.');
            SEOTools::setCanonical(url()->current());

            OpenGraph::setTitle('Trang chủ - Akay Truyện');
            OpenGraph::setDescription('Đọc truyện miễn phí với giao diện đẹp, cập nhật nhanh nhất.');
            OpenGraph::setUrl(url()->current());
            OpenGraph::addProperty('type', 'website');

            TwitterCard::setTitle('Trang chủ - Akay Truyện');
            TwitterCard::setSite('@AkayTruyen');
        }

        // $setting = Helper::getCachedSetting();

        // $objectSEO = (object) [
        //     'name' => $setting ? $setting->title : 'Akay Truyện',
        //     'description' => $setting ? $setting->description : 'Đọc truyện online, truyện hay. Akay Truyện luôn tổng hợp và cập nhật các chương truyện một cách nhanh nhất.',
        //     'keywords' => 'doc truyen, doc truyen online, truyen hay, truyen chu',
        //     'no_index' => $setting ? !$setting->index : env('NO_INDEX'),
        //     'meta_type' => 'WebPage',
        //     'url_canonical' => route('home'),
        //     'image' => asset('images/logo/Logoakay.png'),
        //     'site_name' => 'Akay Truyện'
        // ];

        // Helper::setSEO($objectSEO);

        $cacheTtl = now()->addMinutes(5);

        $storiesHot = Cache::remember('home:stories_hot', $cacheTtl, function () {
            return $this->storyRepository->getStoriesHot(16);
        });

        $storiesNewIds = Cache::remember('home:stories_new_ids', $cacheTtl, function () {
            return $this->storyRepository->getStoriesNewIds()->toArray();
        });
        $storiesNew = Cache::remember('home:stories_new', $cacheTtl, function () use ($storiesNewIds) {
            return $this->storyRepository->getStoriesNew($storiesNewIds);
        });

        $storiesFullIds = Cache::remember('home:stories_full_ids', $cacheTtl, function () {
            return $this->storyRepository->getStoriesFullIds()->toArray();
        });
        $storiesFull = Cache::remember('home:stories_full', $cacheTtl, function () use ($storiesFullIds) {
            return $this->storyRepository->getStoriesFull($storiesFullIds);
        });

        // Ensure chapter_last is available for backward compatibility
        $storiesNew->each(function ($story) {
            if (!$story->chapter_last) {
                $story->chapter_last = $story->latestChapter ?? null;
            }
        });

        $storiesFull->each(function ($story) {
            if (!$story->chapter_last) {
                $story->chapter_last = $story->latestChapter ?? null;
            }
        });

        $totalStory = Cache::remember('stats:total_story', $cacheTtl, fn () => Story::query()->count());
        $totalChapter = Cache::remember('stats:total_chapter', $cacheTtl, fn () => Chapter::query()->count());
        $totalViews = Cache::remember('stats:total_views', $cacheTtl, fn () => Chapter::query()->sum('views'));
        $totalRating = Cache::remember('stats:total_rating', $cacheTtl, fn () => User::query()->sum('rating'));

        $selectedMonth = (int) $request->query('month', Carbon::now()->month);
        $selectedYear = (int) $request->query('year', Carbon::now()->year);
        $selectedMonth = $selectedMonth ?: (int) Carbon::now()->month;
        $selectedYear = $selectedYear ?: (int) Carbon::now()->year;

        // Lấy danh sách các tháng có donate (tối ưu query)
        $months = Cache::remember('donors:months', $cacheTtl, function () {
            return Donation::selectRaw('MONTH(donated_at) as month, YEAR(donated_at) as year')
                ->groupBy('month', 'year')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
        });

        $topDonors = Cache::remember("donors:top:{$selectedYear}-{$selectedMonth}", $cacheTtl, function () use ($selectedMonth, $selectedYear) {
            return Donation::query()
                ->whereMonth('donated_at', $selectedMonth)
                ->whereYear('donated_at', $selectedYear)
                ->selectRaw('name, SUM(amount) as donate_amount')
                ->groupBy('name')
                ->orderByDesc('donate_amount')
                ->get();
        });

        return view('Frontend.home', compact(
            'storiesHot',
            'storiesNew',
            'storiesFull',
            'totalStory',
            'totalChapter',
            'totalViews',
            'totalRating',
            'topDonors',
            'months',
            'selectedMonth',
            'selectedYear'
        ));
    }

    public function getListStoryHot(Request $request)
    {
        $res = ['success' => false];

        $categoryIdInput = $request->input('category_id');
        // $category = $this->categoryRepository->find($categoryId, ['stories']);

        if ($categoryIdInput === 'all' || intval($categoryIdInput) === 0) {
            $stories = Cache::remember('home:stories_hot:all', now()->addMinutes(5), function () {
                return Story::with(['categories', 'latestChapter'])
                    ->withCount('chapters')
                    ->where('status', Story::STATUS_ACTIVE)
                    ->limit(16)
                    ->orderByDesc('updated_at')
                    ->get();
            });
            $categoryId = 0; // hoặc gán giá trị phù hợp cho giao diện
        } else {
            $categoryId = intval($categoryIdInput);
            $category = $this->categoryRepository->find($categoryId, ['stories']);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Danh mục không tồn tại'
                ]);
            }

            // Nếu lấy theo danh mục thì giới hạn 16 story
            $stories = Cache::remember("home:stories_hot:category:{$categoryId}", now()->addMinutes(5), function () use ($category) {
                return $category->stories()
                    ->with(['categories', 'latestChapter'])
                    ->withCount('chapters')
                    ->where('status', Story::STATUS_ACTIVE)
                    ->limit(16)
                    ->orderByDesc('updated_at')
                    ->get();
            });
        }

        $param = [
            'categoryIdSelected' => $categoryId,
            'categories' => Helper::getCachedCategories(),
            'storiesHot' => $stories
        ];
        $html = view('Frontend.sections.main.stories_hot', $param)->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function searchStory(Request $request)
    {
        $res = ['success' => false];

        $stories = $this->storyRepository->getStoryWithByKeyWord($request->input('key_word'));

        $res['success'] = true;
        $res['stories'] = $stories;

        return response()->json($res);
    }

    public function mainSearchStory(Request $request)
    {
        $keyWord = $request->get('key_word');
        $stories = $this->storyRepository->getStoryWithByKeyWord($keyWord);

        // SEO for search page
        $title = "Tìm kiếm: {$keyWord} - Akay Truyện";
        $description = "Kết quả tìm kiếm cho từ khóa '{$keyWord}'. Tìm thấy " . count($stories) . " truyện phù hợp.";
        
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOMeta::setKeywords("tìm kiếm, {$keyWord}, doc truyen, doc truyen online, truyen hay, truyen chu");
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

        $storiesIds = [];
        if (count($stories) > 0) {
            foreach ($stories as $story) {
                $storiesIds[] = $story->id;
            }
        }

        $data = [
            'stories' => $stories,
            'keyWord' => $keyWord
        ];
        return view('Frontend.main_search', $data);
    }
    public function searchChapters(Request $request, $slug)
    {
        try {
            $searchTerm = $request->search;

            $story = Story::select('id', 'slug', 'name')->where('slug', $slug)->firstOrFail();

            $decodedSearchTerm = html_entity_decode($searchTerm, ENT_QUOTES, 'UTF-8');
            $cleanedSearchTerm = $this->cleanWordText($decodedSearchTerm);
            $normalizedSearchTerm = strtolower(trim($cleanedSearchTerm));

            $query = Chapter::select('id', 'story_id', 'slug', 'chapter', 'name', 'content', 'created_at')
                ->where('story_id', $story->id)
                ->published();

            if (!empty($normalizedSearchTerm)) {
                $searchNumber = preg_replace('/[^0-9]/', '', $normalizedSearchTerm);

                $query->where(function ($q) use ($normalizedSearchTerm, $searchNumber) {
                    $q->whereRaw("LOWER(CONVERT(name USING utf8mb4)) LIKE ?", ["%{$normalizedSearchTerm}%"]);

                    if (strlen($normalizedSearchTerm) <= 50) {
                        $q->orWhereRaw("LOWER(CONVERT(content USING utf8mb4)) LIKE ?", ["%{$normalizedSearchTerm}%"]);
                    }

                    if ($searchNumber !== '') {
                        $q->orWhere('chapter', $searchNumber);
                    }
                });
            }

            $chapters = $query->with(['story' => function($q) {
                    $q->select('id', 'slug', 'name');
                }])
                ->orderBy('chapter', 'desc')
                ->paginate(50);


            return response()->json([
                'html' => view('Frontend.components.search-results', compact('chapters', 'story'))->render()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    private function cleanWordText($text)
    {
        $search = [
            "\xC2\xAB",
            "\xC2\xBB", // « »
            "\xE2\x80\x98",
            "\xE2\x80\x99", // ‘ ’ (nháy đơn)
            "\xE2\x80\x9C",
            "\xE2\x80\x9D", // “ ” (nháy kép)
            "\xE2\x80\x93",
            "\xE2\x80\x94", // – — (gạch ngang)
            "\xE2\x80\xA6", // … (dấu ba chấm)
        ];

        $replace = [
            "<<",
            ">>",
            "'",
            "'",
            '"',
            '"',
            "-",
            "-",
            "...",
        ];

        $text = trim($text);

        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        return str_replace($search, $replace, $text);
    }
}
