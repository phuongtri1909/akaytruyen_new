<?php

namespace App\Observers;

use App\Models\UserTagged;
use Illuminate\Support\Facades\Cache;

class UserTaggedObserver
{
    public function created(UserTagged $tag): void
    {
        $this->flushUser($tag->user_id);
    }

    public function updated(UserTagged $tag): void
    {
        $this->flushUser($tag->user_id);
    }

    public function deleted(UserTagged $tag): void
    {
        $this->flushUser($tag->user_id);
    }

    protected function flushUser(?int $userId): void
    {
        if (!$userId) {
            return;
        }
        Cache::forget("notifications:tagged:user:{$userId}");
    }
}

