<?php

namespace App\Observers;

use App\Models\LiveChat;
use Illuminate\Support\Facades\Cache;

class LiveChatObserver
{
    public function created(LiveChat $comment): void
    {
        // New top-level comment increases count
        if (is_null($comment->parent_id)) {
            Cache::forget('livechat:main_count');
            $this->forgetMainLists();
        } else {
            // Reply added affects replies shown in cached main list entries
            $this->forgetMainLists();
        }
    }

    public function updated(LiveChat $comment): void
    {
        // If parent_id toggled between null and not-null, invalidate count
        if ($comment->wasChanged('parent_id')) {
            Cache::forget('livechat:main_count');
            $this->forgetMainLists();
        }

        // Reordering due to pinned changes also affects main list ordering
        if ($comment->wasChanged('pinned')) {
            $this->forgetMainLists();
        }
    }

    public function deleted(LiveChat $comment): void
    {
        // Removing a top-level comment decreases count
        if (is_null($comment->parent_id)) {
            Cache::forget('livechat:main_count');
            $this->forgetMainLists();
        } else {
            // Reply removal affects replies shown in cached main list entries
            $this->forgetMainLists();
        }
    }

    protected function forgetMainLists(): void
    {
        foreach ([10, 20, 30, 40, 50, 100] as $limit) {
            Cache::forget("livechat:main:list:limit:{$limit}");
        }
    }
}

