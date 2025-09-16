<?php

namespace App\Repositories\Story;

use App\Models\Story;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StoryRepository extends BaseRepository implements StoryRepositoryInterface
{

    /**
     * @return mixed|\Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return new Story();
    }

    public function getStoriesActive()
    {
        return $this->getModel()
            ::query()
            ->with(['categories', 'latestChapter'])
            ->withCount('chapters')
            ->where('status', '=', Story::STATUS_ACTIVE)
            ->orderByDesc('updated_at')
            ->get();
    }

    public function getStoriesHot($limit)
    {
        return $this->getModel()
            ::query()
            ->with(['categories', 'latestChapter'])
            ->withCount('chapters')
            ->where('status', '=', Story::STATUS_ACTIVE)
            ->where('is_hot', '=', Story::IS_HOT)
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

    public function getStoriesNewOld()
    {
        // DB::table('stories')
        //     ->select('stories.*', 'categories.name as category_name')
        //     ->join(DB::raw('(SELECT story_id, MAX(id) as max_id FROM chapters GROUP BY story_id) as latestChapters'), function ($join) {
        //         $join->on('latestChapters.story_id', '=', 'stories.id');
        //     })
        //     ->join('chapters', function ($join) {
        //         $join->on('latestChapters.max_id', '=', 'chapters.id');
        //     })
        //     ->join('category_story', 'stories.id', '=', 'category_story.story_id')
        //     ->join('categories', 'category_story.category_id', '=', 'categories.id')
        //     ->addSelect(DB::raw('MAX(chapters.id) as max_chapter_id'))
        //     ->groupBy('stories.id', 'categories.name')
        //     ->get();

        return DB::table('stories')
            ->where('stories.status', '=', Story::STATUS_ACTIVE)
            ->select('stories.*', 'categories.name as category_name')
            ->join(DB::raw('(SELECT story_id, MAX(id) as max_id FROM chapters GROUP BY story_id) as latestChapters'), function ($join) {
                $join->on('latestChapters.story_id', '=', 'stories.id');
            })
            ->join('chapters', function ($join) {
                $join->on('latestChapters.max_id', '=', 'chapters.id');
            })
            ->join('categorie_storie', 'stories.id', '=', 'categorie_storie.storie_id')
            ->join('categories', 'categorie_storie.categorie_id', '=', 'categories.id')
            ->addSelect(DB::raw('MAX(chapters.id) as max_chapter_id'), 'chapters.name as chapter_last_name')
            ->groupBy('stories.id', 'category_name')
            ->orderBy('stories.id', 'desc')
            ->get();
    }

    public function getStoriesNew($ids)
    {
        $now = Carbon::now()->toDateTimeString();
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now()->subHours(24))->toDateTimeString();

        return $this->getModel()
            ->query()
            ->with(['categories', 'latestChapter'])
            ->withCount('chapters')
            ->whereIn('id', $ids)
            ->where('is_new', '=', Story::IS_NEW)
            // ->where('updated_at', '>=', $startDate)
            // ->where('updated_at', '<=', $now)
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();
    }

    public function getStoriesNewIds()
    {
        return $this->getModel()
            ->query()
            ->where('status', '=', Story::STATUS_ACTIVE)
            ->where('is_new', '=', Story::IS_NEW)
            ->pluck('id');
    }

    public function getStoriesFull($ids)
    {
        return $this->getModel()
            ->query()
            ->with(['categories', 'latestChapter'])
            ->withCount('chapters')
            ->where('is_full', '=', Story::FULL)
            ->whereIn('id', $ids)
            ->where('status', '=', Story::STATUS_ACTIVE)
            ->orderByDesc('updated_at')
            ->get();
    }

    public function getStoriesFullIds()
    {
        return $this->getModel()
            ->query()
            ->where('is_full', '=', Story::FULL)
            ->where('status', '=', Story::STATUS_ACTIVE)
            ->pluck('id');
    }

    public function getStoryBySlug($slug, $with = [])
    {
        $relations = array_values(array_unique(array_merge($with, ['latestChapter'])));

        return $this->getModel()
            ->query()
            ->with($relations)
            ->where('slug', '=', $slug)
            ->where('status', '=', Story::STATUS_ACTIVE)
            ->first();
    }

    public function getStoryBySlugOptimized($slug)
    {
        return $this->getModel()
            ->query()
            ->with([
                'categories',
                'author',
                'author.stories' => function($query) {
                    $query->select('id', 'name', 'slug', 'author_id', 'is_new', 'is_full')
                          ->where('status', Story::STATUS_ACTIVE);
                },
                'latestChapter'
            ])
            ->where('slug', '=', $slug)
            ->where('status', '=', Story::STATUS_ACTIVE)
            ->first();
    }

    public function getCachedStoryDetail($slug)
    {
        $user = auth()->user();
        $banSuffix = $user && $user->userBan && $user->userBan->read ? ':banned' : '';
        $cacheKey = "story:detail:{$slug}" . $banSuffix;
        
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($slug) {
            return $this->getStoryBySlugOptimized($slug);
        });
    }

    public function getCachedStoryChapters($storyId, $page = 1, $isOldFirst = false)
    {
        $user = auth()->user();
        $banSuffix = $user && $user->userBan && $user->userBan->read ? ':banned' : '';
        $cacheKey = "story:chapters:{$storyId}:page:{$page}:order:" . ($isOldFirst ? 'asc' : 'desc') . $banSuffix;

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($storyId, $isOldFirst) {
            $chapters = $this->getModel()
                ->find($storyId)
                ->chapters()
                ->orderBy('chapter', $isOldFirst ? 'asc' : 'desc')
                ->paginate(50);
            
            $user = auth()->user();
            if ($user && $user->userBan && $user->userBan->read) {
                $chapters->getCollection()->transform(function ($chapter) {
                    $chapter->setAttribute('content', null);
                    return $chapter;
                });
            }
            
            return $chapters;
        });
    }

    public function getCachedStoryStats($storyId)
    {
        return Cache::remember("story:stats:{$storyId}", now()->addMinutes(30), function () use ($storyId) {
            $totalChapters = \App\Models\Chapter::where('story_id', $storyId)->count();
            $totalViews = \App\Models\Chapter::where('story_id', $storyId)->sum('views');

            return [
                'total_chapters' => $totalChapters,
                'total_views' => $totalViews
            ];
        });
    }

    public function getCachedChapterRanges($storyId)
    {
        return Cache::remember("story:chapter_ranges:{$storyId}", now()->addMinutes(60), function () use ($storyId) {
            // Single query to get both min and max
            $result = \App\Models\Chapter::where('story_id', $storyId)
                ->selectRaw('MIN(chapter) as min_chapter, MAX(chapter) as max_chapter')
                ->first();

            if (!$result || !$result->max_chapter) {
                return [];
            }

            $maxNumber = $result->max_chapter;
            $minNumber = $result->min_chapter ?? 0;
            $ranges = [];

            // Chia chương thành các đoạn 50 chương
            $totalChunks = ceil(($maxNumber - $minNumber + 1) / 50);

            for ($i = 0; $i < $totalChunks; $i++) {
                $start = $maxNumber - ($i * 50);
                $end = max($start - 49, $minNumber);
                $ranges[] = ["start" => $end, "end" => $start];
            }

            return $ranges;
        });
    }

    public function getStoriesHotRandom($limit)
    {
        return $this->getModel()
            ->query()
            ->with(['categories', 'latestChapter'])
            ->withCount('chapters')
            ->inRandomOrder()
            ->where('status', '=', Story::STATUS_ACTIVE)
            ->where('is_hot', '=', Story::IS_HOT)
            ->limit($limit)
            ->get();
    }

    public function getStoryWithByKeyWord($keyWord)
    {
        return $this->getModel()
            ->query()
            ->with(['author', 'latestChapter'])
            ->withCount('chapters')
            ->where('name', 'LIKE', '%' . $keyWord . '%')
            ->get();
    }

    public function getStoriesWithChaptersCount($value)
    {
        return $this->getModel()
            ::query()
            ->with(['categories', 'latestChapter'])
            ->withCount('chapters')
            ->where('status', '=', Story::STATUS_ACTIVE)
            ->having('chapters_count', '>=', $value[0])
            ->having('chapters_count', '<=', $value[1])
            ->orderByDesc('updated_at')
            ->get();
    }
}
