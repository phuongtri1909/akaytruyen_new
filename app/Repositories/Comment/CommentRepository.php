<?php

namespace App\Repositories\Comment;

use App\Models\Comment;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    /**
     * @return mixed|\Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return new Comment();
    }

    public function getChapterComments($chapterId, $isPinned = false, $limit = 50)
    {
        return $this->getModel()
            ->query()
            ->where('chapter_id', $chapterId)
            ->whereNull('reply_id')
            ->where('is_pinned', $isPinned)
            ->with([
                'user:id,name,email,avatar',
                'user.roles:id,name,guard_name',
                'parent:id,reply_id,user_id',
                'parent.user:id,name,email,avatar',
                'parent.user.roles:id,name,guard_name',
                'editor:id,name,email,avatar',
                'editHistories.editor:id,name,email,avatar',
                'replies' => function($query) {
                    $query->with([
                        'user:id,name,email,avatar',
                        'user.roles:id,name,guard_name',
                        'editor:id,name,email,avatar',
                        'editHistories.editor:id,name,email,avatar',
                        'reactions:id,comment_id,type,user_id',
                        'replies' => function($subQuery) {
                            $subQuery->with([
                                'user:id,name,email,avatar',
                                'user.roles:id,name,guard_name',
                                'editor:id,name,email,avatar',
                                'editHistories.editor:id,name,email,avatar',
                                'reactions:id,comment_id,type,user_id'
                            ])->oldest();
                        }
                    ])->oldest();
                },
                'reactions:id,comment_id,type,user_id'
            ])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getCachedChapterComments($chapterId, $isPinned = false, $limit = 50)
    {
        $cacheKey = "chapter:comments:{$chapterId}:pinned:" . ($isPinned ? 'true' : 'false') . ":limit:{$limit}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($chapterId, $isPinned, $limit) {
            return $this->getChapterComments($chapterId, $isPinned, $limit);
        });
    }

    public function getChapterCommentsPaginated($chapterId, $isPinned = false, $perPage = 10)
    {
        return $this->getModel()
            ->query()
            ->where('chapter_id', $chapterId)
            ->whereNull('reply_id')
            ->where('is_pinned', $isPinned)
            ->with([
                'user:id,name,email,avatar',
                'user.roles:id,name,guard_name',
                'parent:id,reply_id,user_id',
                'parent.user:id,name,email,avatar',
                'parent.user.roles:id,name,guard_name',
                'editor:id,name,email,avatar',
                'editHistories.editor:id,name,email,avatar',
                'replies' => function($query) {
                    $query->with([
                        'user:id,name,email,avatar',
                        'user.roles:id,name,guard_name',
                        'editor:id,name,email,avatar',
                        'editHistories.editor:id,name,email,avatar',
                        'reactions:id,comment_id,type,user_id',
                        'replies' => function($subQuery) {
                            $subQuery->with([
                                'user:id,name,email,avatar',
                                'user.roles:id,name,guard_name',
                                'editor:id,name,email,avatar',
                                'editHistories.editor:id,name,email,avatar',
                                'reactions:id,comment_id,type,user_id'
                            ])->oldest();
                        }
                    ])->oldest();
                },
                'reactions:id,comment_id,type,user_id'
            ])
            ->latest()
            ->paginate($perPage);
    }
}
