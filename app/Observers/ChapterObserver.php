<?php

namespace App\Observers;

use App\Models\Chapter;
use Illuminate\Support\Facades\Cache;

class ChapterObserver
{
    public function created(Chapter $chapter): void {
        $this->flushChapterSpecific($chapter);
        $this->flushGlobal();
    }

    public function updated(Chapter $chapter): void {
        $changedKeys = array_keys($chapter->getChanges());
        $nonCritical = ['views', 'updated_at'];
        $meaningfulChanges = array_diff($changedKeys, $nonCritical);

        if (empty($meaningfulChanges)) {
            // Skip cache flush for view-only updates to avoid thrashing
            return;
        }

        $this->flushChapterSpecific($chapter);

        // Only flush global stats if chapter count affecting fields changed
        $globalAffectingFields = ['chapter', 'name', 'is_new', 'story_id'];
        $affectsGlobal = !empty(array_intersect($changedKeys, $globalAffectingFields));

        if ($affectsGlobal) {
            $this->flushGlobal();
        }
    }

    public function deleted(Chapter $chapter): void {
        $this->flushChapterSpecific($chapter);
        $this->flushGlobal();
    }

    protected function flushChapterSpecific(Chapter $chapter): void
    {
        if (!$chapter->story_id) return;

        $story = $chapter->story;
        if ($story) {
            $this->clearCacheVariants("story:detail:{$story->slug}");
            Cache::forget("story:stats:{$chapter->story_id}");
            
            $this->clearCacheVariants("story:chapters_new:{$chapter->story_id}");
            Cache::forget("story:chapter_ranges:{$chapter->story_id}");

            $this->clearCacheVariants("chapter:with_nav:{$chapter->story_id}:{$chapter->slug}");

            $this->clearCacheVariants("chapter:data:{$chapter->story_id}:{$chapter->slug}");

            Cache::forget("chapter:last:{$chapter->story_id}");

            $adjacentChapters = [$chapter->chapter - 1, $chapter->chapter + 1];
            foreach ($adjacentChapters as $chapterNum) {
                if ($chapterNum > 0) {
                    $adjacentChapter = \App\Models\Chapter::where('story_id', $chapter->story_id)
                        ->where('chapter', $chapterNum)->first();
                    if ($adjacentChapter) {
                        $this->clearCacheVariants("chapter:with_nav:{$chapter->story_id}:{$adjacentChapter->slug}");
                    }
                }
            }

            for ($page = 1; $page <= 10; $page++) {
                $this->clearCacheVariants("story:chapters:{$chapter->story_id}:page:{$page}:order:asc");
                $this->clearCacheVariants("story:chapters:{$chapter->story_id}:page:{$page}:order:desc");
            }
        }
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

    protected function flushGlobal(): void
    {
        Cache::forget('home:stories_hot');
        Cache::forget('home:stories_new');
        Cache::forget('home:stories_full');
        Cache::forget('stats:total_chapter');
        Cache::forget('home:stories_hot:all');
        Cache::forget('app:stats');
    }
}


