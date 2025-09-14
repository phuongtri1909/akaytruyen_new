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

        // Clear global caches if status, visibility, category-related fields, or image changed
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
        // Clear story-specific caches
        Cache::forget("story:detail:{$story->slug}");
        Cache::forget("story:stats:{$story->id}");
        Cache::forget("story:chapters_new:{$story->id}");
        Cache::forget("story:chapter_ranges:{$story->id}");

        // Clear story chapters pagination cache (all pages and orders)
        $patterns = [
            "story:chapters:{$story->id}:page:*:order:*"
        ];

        foreach ($patterns as $pattern) {
            // Since Laravel doesn't support wildcard forget, we'll need to clear specific pages
            // This is a simple approach - in production you might want to use cache tags
            for ($page = 1; $page <= 10; $page++) { // Clear first 10 pages
                Cache::forget("story:chapters:{$story->id}:page:{$page}:order:asc");
                Cache::forget("story:chapters:{$story->id}:page:{$page}:order:desc");
            }
        }

        // Clear category-specific caches that might include this story
        if ($story->categories) {
            foreach ($story->categories as $category) {
                Cache::forget("home:stories_hot:category:{$category->id}");
            }
        }

        // Clear all stories_hot caches to ensure image updates are reflected
        Cache::forget('home:stories_hot');
        Cache::forget('home:stories_hot:all');

        // Clear all category caches to handle story deletion/updates
        $this->clearAllCategoryCaches();
    }

    protected function clearAllCategoryCaches(): void
    {
        // Clear all category-specific caches
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

        // Clear all category caches
        $this->clearAllCategoryCaches();
    }
}


