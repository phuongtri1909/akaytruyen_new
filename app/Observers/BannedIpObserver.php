<?php

namespace App\Observers;

use App\Models\BanIp;
use Illuminate\Support\Facades\Cache;

class BannedIpObserver
{
    public function created(BanIp $bannedIp): void { $this->flush($bannedIp); }
    public function updated(BanIp $bannedIp): void { $this->flush($bannedIp); }
    public function deleted(BanIp $bannedIp): void { $this->flush($bannedIp); }

    protected function flush(BanIp $bannedIp): void
    {
        // Clear specific IP cache
        Cache::forget("banned_ip:{$bannedIp->ip_address}");

        // Also clear any cached user-related data if user_id exists
        if ($bannedIp->user_id) {
            Cache::forget("banned_ip:user:{$bannedIp->user_id}");
        }
    }
}
