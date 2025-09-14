<?php

namespace App\Observers;

use App\Models\Donation;
use Illuminate\Support\Facades\Cache;

class DonationObserver
{
    public function created(Donation $donation): void
    {
        $this->flush($donation);
    }

    public function updated(Donation $donation): void
    {
        $this->flush($donation);
    }

    public function deleted(Donation $donation): void
    {
        $this->flush($donation);
    }

    protected function flush(Donation $donation): void
    {
        // Clear cache cho trang chủ (tổng hợp tất cả truyện)
        Cache::forget('donors:months');

        // Clear cache cho từng tháng của trang chủ
        $this->clearHomePageCache();

        // Clear cache cho truyện cụ thể nếu có story_id
        if ($donation->story_id) {
            $this->clearStoryCache($donation->story_id);
        }

        // Clear cache cho thống kê tổng
        Cache::forget('stats:total_donation');
    }

    protected function clearHomePageCache(): void
    {
        // Clear cache cho tất cả tháng có thể có
        for ($year = date('Y') - 2; $year <= date('Y') + 1; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                Cache::forget("donors:top:{$year}-{$month}");
            }
        }
    }

    protected function clearStoryCache(int $storyId): void
    {
        // Clear cache cho truyện cụ thể
        Cache::forget("story:{$storyId}:donations:months");

        // Clear cache cho từng tháng của truyện cụ thể
        for ($year = date('Y') - 2; $year <= date('Y') + 1; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                Cache::forget("story:{$storyId}:donors:top:{$year}-{$month}");
            }
        }
    }
}


