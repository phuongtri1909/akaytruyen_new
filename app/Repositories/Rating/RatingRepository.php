<?php

namespace App\Repositories\Rating;

use App\Models\Rating;
use App\Models\Story;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RatingRepository extends BaseRepository implements RatingRepositoryInterface
{

    /**
     * @return mixed|\Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return new Rating();
    }

    public function getRatingByType($type)
    {
        return Cache::remember("ratings:type:{$type}", now()->addMinutes(60), function () use ($type) {
            return $this->getModel()->query()->where('type', '=', $type)->first();
        });
    }

    public function getStories($arrStoryIds)
    {
        if (empty($arrStoryIds)) {
            return collect();
        }

        $cacheKey = 'ratings:stories:' . md5(implode(',', $arrStoryIds));

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($arrStoryIds) {
            return Story::query()
                ->with(['categories', 'latestChapter'])
                ->withCount('chapters')
                ->whereIn('id', $arrStoryIds)
                ->orderByRaw('FIELD(id, ' . implode(',', $arrStoryIds) . ')')
                ->get();
        });
    }
}
