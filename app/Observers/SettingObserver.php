<?php

namespace App\Observers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingObserver
{
    public function created(Setting $setting): void { $this->flush(); }
    public function updated(Setting $setting): void { $this->flush(); }
    public function deleted(Setting $setting): void { $this->flush(); }

    protected function flush(): void
    {
        // Clear app stats cache when settings change
        Cache::forget('app:stats');
        Cache::forget('app:setting');
    }
}
