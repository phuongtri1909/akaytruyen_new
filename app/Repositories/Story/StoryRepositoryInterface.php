<?php

namespace App\Repositories\Story;

use App\Repositories\BaseRepositoryInterface;

interface StoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getStoriesActive();
    public function getStoriesHot($limit);
    public function getStoriesNew($ids);
    public function getStoriesNewIds();
    public function getStoriesFull($ids);
    public function getStoriesFullIds();
    public function getStoryBySlug($slug);
    public function getStoryBySlugOptimized($slug);
    public function getCachedStoryDetail($slug);
    public function getCachedStoryChapters($storyId, $page = 1, $isOldFirst = false);
    public function getCachedStoryStats($storyId);
    public function getCachedChapterRanges($storyId);
    public function getStoriesHotRandom($limit);
    public function getStoryWithByKeyWord($keyWord);
    public function getStoriesWithChaptersCount($value);
}
