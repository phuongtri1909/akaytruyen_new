<?php

namespace App\Repositories\Chapter;

use App\Repositories\BaseRepositoryInterface;

interface ChapterRepositoryInterface extends BaseRepositoryInterface
{
    public function getChapterLast($storyIds);
    public function getChaptersByStoryId($storyId);
    public function getListChapterByStoryId($storyId);
    public function getChaptersNewByStoryId($storyId);
    public function getChapterSingle($storyId, $slug);
    public function getCachedChapterWithNavigation($storyId, $slug);
    public function getCachedChapterData($storyId, $slugChapter);
    public function getChapterLastSingle($storyId);
    public function findBySlug(string $slug);
    public function findBySlugExcept(string $slug, int $id);

}
