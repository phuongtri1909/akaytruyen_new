<?php

namespace App\Repositories\Comment;

use App\Repositories\BaseRepositoryInterface;

interface CommentRepositoryInterface extends BaseRepositoryInterface
{
    public function getChapterComments($chapterId, $isPinned = false, $limit = 50);
    public function getCachedChapterComments($chapterId, $isPinned = false, $limit = 50);
    public function getChapterCommentsPaginated($chapterId, $isPinned = false, $perPage = 10);
}
