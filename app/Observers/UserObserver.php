<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    public function created(User $user): void { $this->flushIfRelevant($user); }
    public function updated(User $user): void { $this->flushIfRelevant($user); }
    public function deleted(User $user): void { $this->flushIfRelevant($user); }

    protected function flushIfRelevant(User $user): void
    {
        $statsAffectingFields = ['active', 'role'];
        $changedKeys = array_keys($user->getChanges());

        $affectsUserStats = !empty(array_intersect($changedKeys, $statsAffectingFields));

        if ($user->wasChanged('rating')) {
            Cache::forget('stats:total_rating');
            Cache::forget('app:stats');
        }

        // Clear user stats cache if relevant fields changed
        if ($affectsUserStats || $user->wasRecentlyCreated) {
            Cache::forget('app:user_stats');
            Cache::forget('app:stats');
        }
    }
}


