<?php

namespace App\Observers;

use App\Models\Comment;
use Illuminate\Support\Facades\Cache;

class CommentObserver
{
    public function created(Comment $comment): void {
        $this->flushCommentSpecific($comment);
    }

    public function updated(Comment $comment): void {
        $this->flushCommentSpecific($comment);
    }

    public function deleted(Comment $comment): void {
        $this->flushCommentSpecific($comment);
    }

    protected function flushCommentSpecific(Comment $comment): void
    {
        if (!$comment->chapter_id) return;

        // Clear chapter-specific comment caches
        $patterns = [
            "chapter:comments:{$comment->chapter_id}:pinned:true:limit:*",
            "chapter:comments:{$comment->chapter_id}:pinned:false:limit:*"
        ];

        // Clear common cache combinations
        $limits = [10, 20, 50, 100];
        foreach ($limits as $limit) {
            Cache::forget("chapter:comments:{$comment->chapter_id}:pinned:true:limit:{$limit}");
            Cache::forget("chapter:comments:{$comment->chapter_id}:pinned:false:limit:{$limit}");
        }

        // If parent comment changes, also clear reply-related caches
        if ($comment->reply_id) {
            $parentComment = Comment::find($comment->reply_id);
            if ($parentComment) {
                foreach ($limits as $limit) {
                    Cache::forget("chapter:comments:{$parentComment->chapter_id}:pinned:true:limit:{$limit}");
                    Cache::forget("chapter:comments:{$parentComment->chapter_id}:pinned:false:limit:{$limit}");
                }
            }
        }
    }
}
