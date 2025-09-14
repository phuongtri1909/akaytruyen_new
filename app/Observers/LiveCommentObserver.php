<?php

namespace App\Observers;

use App\Models\LiveComment;
use Illuminate\Support\Facades\Cache;

class LiveCommentObserver
{
    public function created(LiveComment $model): void { $this->flush(); }
    public function updated(LiveComment $model): void { $this->flush(); }
    public function deleted(LiveComment $model): void { $this->flush(); }

    protected function flush(): void
    {
        Cache::forget('comments:pinned');
    }
}


