<?php

namespace App\Observers;

use App\Models\Notification;
use Illuminate\Support\Facades\Cache;

class NotificationObserver
{
    public function created(Notification $notification): void
    {
        $this->flushUser($notification->user_id);
    }

    public function updated(Notification $notification): void
    {
        $this->flushUser($notification->user_id);
    }

    public function deleted(Notification $notification): void
    {
        $this->flushUser($notification->user_id);
    }

    protected function flushUser(?int $userId): void
    {
        if (!$userId) {
            return;
        }
        Cache::forget("notifications:unread:user:{$userId}");
    }
}

