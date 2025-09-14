<?php

namespace App\Observers;

use App\Models\Status;
use Illuminate\Support\Facades\Cache;

class StatusObserver
{
    public function created(Status $status): void { $this->flush(); }
    public function updated(Status $status): void { $this->flush(); }
    public function deleted(Status $status): void { $this->flush(); }

    protected function flush(): void
    {
        // Clear app status cache when status changes
        Cache::forget('app:status');
    }
}
