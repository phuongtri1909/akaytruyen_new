<?php

namespace App\Observers;

use App\Models\Story;
use Illuminate\Support\Facades\Cache;

class StoryObserver
{
    public function created(Story $story): void {
        $this->flushStorySpecific($story);
        $this->flushGlobal();
    }

    public function updated(Story $story): void {
        // Check what fields actually changed
        $changedKeys = array_keys($story->getChanges());
        $nonCritical = ['views', 'updated_at'];
        $meaningfulChanges = array_diff($changedKeys, $nonCritical);

        if (empty($meaningfulChanges)) {
            // Skip cache flush for view-only updates to avoid thrashing
            return;
        }

        $this->flushStorySpecific($story);

        $globalAffectingFields = ['status', 'is_hot', 'is_new', 'is_full', 'name', 'slug', 'author_id', 'image'];
        $affectsGlobal = !empty(array_intersect($changedKeys, $globalAffectingFields));

        if ($affectsGlobal) {
            $this->flushGlobal();
        }
    }

    public function deleted(Story $story): void {
        $this->flushStorySpecific($story);
        $this->flushGlobal();
    }

    protected function flushStorySpecific(Story $story): void
    {
        $this->clearCacheVariants("story:detail:{$story->slug}");
        Cache::forget("story:stats:{$story->id}");
        $this->clearCacheVariants("story:chapters_new:{$story->id}");
        Cache::forget("story:chapter_ranges:{$story->id}");

        for ($page = 1; $page <= 10; $page++) {
            $this->clearCacheVariants("story:chapters:{$story->id}:page:{$page}:order:asc");
            $this->clearCacheVariants("story:chapters:{$story->id}:page:{$page}:order:desc");
        }

        if ($story->categories) {
            foreach ($story->categories as $category) {
                Cache::forget("home:stories_hot:category:{$category->id}");
            }
        }

        Cache::forget('home:stories_hot');
        Cache::forget('home:stories_hot:all');

        $this->clearAllCategoryCaches();
    }

        protected function clearCacheVariants(string $baseKey): void
        {
            Cache::forget($baseKey);
            Cache::forget($baseKey . ':banned');
            Cache::forget($baseKey . ':novip');
            Cache::forget($baseKey . ':guest');
            Cache::forget($baseKey . ':banned:novip');
            Cache::forget($baseKey . ':banned:guest');
            Cache::forget($baseKey . ':novip:guest');
            Cache::forget($baseKey . ':banned:novip:guest');
        }

    protected function clearAllCategoryCaches(): void
    {
        $categories = \App\Models\Category::all();
        foreach ($categories as $category) {
            Cache::forget("home:stories_hot:category:{$category->id}");
        }
    }

    protected function flushGlobal(): void
    {
        Cache::forget('home:stories_hot');
        Cache::forget('home:stories_new_ids');
        Cache::forget('home:stories_new');
        Cache::forget('home:stories_full_ids');
        Cache::forget('home:stories_full');
        Cache::forget('stats:total_story');
        Cache::forget('home:stories_hot:all');
        Cache::forget('app:stats');

        $this->clearAllCategoryCaches();
    }
}


