<?php

namespace App\Observers;

use App\Models\Rating;
use Illuminate\Support\Facades\Cache;

class RatingObserver
{
    public function created(Rating $rating): void {
        $this->flushRatingSpecific($rating);
        $this->flushGlobal();
    }

    public function updated(Rating $rating): void {
        $this->flushRatingSpecific($rating);
        $this->flushGlobal();
    }

    public function deleted(Rating $rating): void {
        $this->flushRatingSpecific($rating);
        $this->flushGlobal();
    }

    protected function flushRatingSpecific(Rating $rating): void
    {
        // Clear rating type cache
        if ($rating->type) {
            Cache::forget("ratings:type:{$rating->type}");
        }

        // Clear story-specific cache if story_id exists
        if ($rating->story_id) {
            Cache::forget("story:stats:{$rating->story_id}");

            // Clear story detail cache to update average rating
            $story = $rating->story;
            if ($story) {
                Cache::forget("story:detail:{$story->slug}");
            }
        }

        // Clear ratings stories cache (this is harder without knowing exact story IDs)
        // We'll need to clear pattern-based cache keys
        $this->clearRatingStoriesCache();
    }

    protected function flushGlobal(): void
    {
        Cache::forget('stats:total_rating');
        Cache::forget('app:stats');
    }

    protected function clearRatingStoriesCache(): void
    {
        // Since we can't easily clear pattern-based cache in Laravel
        // We'll clear known rating types that might be affected
        $types = [Rating::TYPE_DAY, Rating::TYPE_MONTH, Rating::TYPE_ALL_TIME];

        foreach ($types as $type) {
            $ratingData = Rating::where('type', $type)->first();
            if ($ratingData && $ratingData->value) {
                $storyIds = collect(json_decode($ratingData->value, true))->pluck('id')->toArray();
                if (!empty($storyIds)) {
                    $cacheKey = 'ratings:stories:' . md5(implode(',', $storyIds));
                    Cache::forget($cacheKey);
                }
            }
        }
    }
}
