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

                        // Clear story-specific caches affected by chapter changes
        $story = $chapter->story;
        if ($story) {
            Cache::forget("story:detail:{$story->slug}");
            Cache::forget("story:stats:{$chapter->story_id}");
            Cache::forget("story:chapters_new:{$chapter->story_id}");
            Cache::forget("story:chapter_ranges:{$chapter->story_id}");

                        // Clear chapter navigation cache
            Cache::forget("chapter:with_nav:{$chapter->story_id}:{$chapter->slug}");

            // Clear chapter data cache
            Cache::forget("chapter:data:{$chapter->story_id}:{$chapter->slug}");

            // Clear chapter last cache
            Cache::forget("chapter:last:{$chapter->story_id}");

            // Clear navigation cache for adjacent chapters
            $adjacentChapters = [$chapter->chapter - 1, $chapter->chapter + 1];
            foreach ($adjacentChapters as $chapterNum) {
                if ($chapterNum > 0) {
                    $adjacentChapter = \App\Models\Chapter::where('story_id', $chapter->story_id)
                        ->where('chapter', $chapterNum)->first();
                    if ($adjacentChapter) {
                        Cache::forget("chapter:with_nav:{$chapter->story_id}:{$adjacentChapter->slug}");
                    }
                }
            }

            // Clear chapter pagination cache for this story
            for ($page = 1; $page <= 10; $page++) {
                Cache::forget("story:chapters:{$chapter->story_id}:page:{$page}:order:asc");
                Cache::forget("story:chapters:{$chapter->story_id}:page:{$page}:order:desc");
            }
        }
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


