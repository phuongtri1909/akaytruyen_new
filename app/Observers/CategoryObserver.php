<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    public function created(Category $category): void { $this->flush(); }
    public function updated(Category $category): void { $this->flush(); }
    public function deleted(Category $category): void { $this->flush(); }

    protected function flush(): void
    {
        // Clear app stats cache when categories change
        Cache::forget('app:stats');
        Cache::forget('app:categories');
    }
}
