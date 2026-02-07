<?php

namespace App\Repositories\Chapter;

use App\Models\Chapter;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ChapterRepository extends BaseRepository implements ChapterRepositoryInterface
{

    /**
     * @return mixed|\Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return new Chapter();
    }

    public function getChapterLast($storyIds)
    {
        if (empty($storyIds)) {
            return collect();
        }

        return DB::table('chapters as c')
            ->join(DB::raw('(SELECT story_id, MAX(id) AS max_id FROM chapters WHERE story_id IN (' . implode(',', $storyIds) . ') GROUP BY story_id) latest_chapters'), 'c.id', '=', 'latest_chapters.max_id')
            ->select('c.*')
            ->get();
    }

    public function getChaptersByStoryId($storyId, $isOldFirst = false)
    {
        return $this->getModel()
            ->query()
            ->where('story_id', $storyId)
            ->published()
            ->orderBy('chapter', $isOldFirst ? 'ASC' : 'DESC')
            ->paginate(50);
    }



    public function getListChapterByStoryId($storyId)
    {
        return $this->getModel()
            ->query()
            ->where('story_id', '=', $storyId)
            ->published()
            ->select(['id', 'name', 'slug', 'chapter'])
            ->get();
    }

    public function getChaptersNewByStoryId($storyId)
    {
        $user = auth()->user();
        $story = \App\Models\Story::find($storyId);
        
        $banSuffix = $user && $user->userBan && $user->userBan->read ? ':banned' : '';
        $vipSuffix = $user && $story && $story->is_vip && !$user->can('xem_chuong_truyen_vip') ? ':novip' : '';
        $guestSuffix = !$user && $story && $story->is_vip ? ':guest' : '';
        $cacheKey = "story:chapters_new:{$storyId}" . $banSuffix . $vipSuffix . $guestSuffix;
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($storyId) {
            $chapters = $this->getModel()
                ->query()
                ->where('story_id', '=', $storyId)
                ->published()
                ->where('is_new', '=', Chapter::IS_NEW)
                ->orderBy('chapter', 'desc')
                ->select('id', 'name', 'slug')
                ->get();

            return $chapters;
        });
    }

    public function getChapterSingle($storyId, $slug)
    {
        return $this->getModel()
            ->query()
            ->where('story_id', '=', $storyId)
            ->where('slug', '=', $slug)
            ->published()
            ->first();
    }

    public function getCachedChapterWithNavigation($storyId, $slug)
    {
        $user = auth()->user();
        $story = \App\Models\Story::find($storyId);
        
        $banSuffix = $user && $user->userBan && $user->userBan->read ? ':banned' : '';
        $vipSuffix = $user && $story && $story->is_vip && !$user->can('xem_chuong_truyen_vip') ? ':novip' : '';
        $guestSuffix = !$user && $story && $story->is_vip ? ':guest' : '';
        $cacheKey = "chapter:with_nav:{$storyId}:{$slug}" . $banSuffix . $vipSuffix . $guestSuffix;

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($storyId, $slug, $user, $story) {
            $chapter = $this->getChapterSingle($storyId, $slug);

            if (!$chapter) {
                return null;
            }

            if ($user) {
                if ($user->userBan && $user->userBan->read) {
                    $chapter->setAttribute('content', null);
                }
                elseif ($story && $story->is_vip && !$user->can('xem_chuong_truyen_vip')) {
                    $chapter->setAttribute('content', null);
                }
            } else {
                if ($story && $story->is_vip) {
                    $chapter->setAttribute('content', null);
                }
            }

            $chapterInt = $chapter->chapter;
            $navigationChapters = $this->getModel()
                ->query()
                ->where('story_id', $storyId)
                ->published()
                ->whereIn('chapter', [$chapterInt - 1, $chapterInt + 1])
                ->select('id', 'slug', 'chapter', 'name')
                ->get()
                ->keyBy('chapter');

            $chapter->chapterBefore = $navigationChapters->get($chapterInt - 1);
            $chapter->chapterAfter = $navigationChapters->get($chapterInt + 1);

            return $chapter;
        });
    }

    public function getChapterLastSingle($storyId)
    {
        return Cache::remember("chapter:last:{$storyId}", now()->addMinutes(60), function () use ($storyId) {
            return $this->getModel()
                ->query()
                ->where('story_id', '=', $storyId)
                ->orderBy('id', 'DESC')
                ->first();
        });
    }

    public function getCachedChapterData($storyId, $slugChapter)
    {
        $user = auth()->user();
        $story = \App\Models\Story::find($storyId);
        
        $banSuffix = $user && $user->userBan && $user->userBan->read ? ':banned' : '';
        $vipSuffix = $user && $story && $story->is_vip && !$user->can('xem_chuong_truyen_vip') ? ':novip' : '';
        $guestSuffix = !$user && $story && $story->is_vip ? ':guest' : '';
        $cacheKey = "chapter:data:{$storyId}:{$slugChapter}" . $banSuffix . $vipSuffix . $guestSuffix;

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($storyId, $slugChapter, $user, $story) {
            $chapter = $this->getModel()
                ->query()
                ->where('story_id', '=', $storyId)
                ->where('slug', '=', $slugChapter)
                ->published()
                ->with(['story:id,name,slug,is_vip'])
                ->first();

            if (!$chapter) {
                return null;
            }

            if ($user) {
                if ($user->userBan && $user->userBan->read) {
                    $chapter->setAttribute('content', null);
                }
                elseif ($story && $story->is_vip && !$user->can('xem_chuong_truyen_vip')) {
                    $chapter->setAttribute('content', null);
                }
            } else {
                if ($story && $story->is_vip) {
                    $chapter->setAttribute('content', null);
                }
            }

            $chapterInt = $chapter->chapter;
            $allChapters = $this->getModel()
                ->query()
                ->where('story_id', $storyId)
                ->published()
                ->where(function($query) use ($chapterInt, $storyId) {
                    $query->whereIn('chapter', [$chapterInt - 1, $chapterInt + 1])
                          ->orWhere('id', function($subQuery) use ($storyId) {
                              $subQuery->select('id')
                                      ->from('chapters')
                                      ->where('story_id', $storyId)
                                      ->orderBy('id', 'DESC')
                                      ->limit(1);
                          });
                })
                ->select('id', 'slug', 'chapter', 'name')
                ->get();

            $navigationChapters = $allChapters->whereIn('chapter', [$chapterInt - 1, $chapterInt + 1])->keyBy('chapter');
            $lastChapter = $allChapters->where('chapter', '!=', $chapterInt - 1)->where('chapter', '!=', $chapterInt + 1)->first();

            $chapter->chapterBefore = $navigationChapters->get($chapterInt - 1);
            $chapter->chapterAfter = $navigationChapters->get($chapterInt + 1);
            $chapter->chapterLast = $lastChapter;

            return $chapter;
        });
    }
    public function findBySlug(string $slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function findBySlugExcept(string $slug, int $id)
    {
        return $this->model->where('slug', $slug)->where('id', '!=', $id)->first();
    }
}
