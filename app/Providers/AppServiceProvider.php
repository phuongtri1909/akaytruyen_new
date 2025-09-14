<?php

namespace App\Providers;

use App\Http\ViewComposers\ChapterComposer;
use App\Http\ViewComposers\HomeComposer;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\ViewComposers\LayoutComposer;
use App\Models\Status;
use App\Models\Chapter;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Story;
use App\Models\Donation;
use App\Models\BanIp;
use App\Observers\StoryObserver;
use App\Observers\ChapterObserver;
use App\Observers\DonationObserver;
use App\Observers\UserObserver;
use App\Observers\CategoryObserver;
use App\Observers\SettingObserver;
use App\Observers\BannedIpObserver;
use App\Observers\StatusObserver;
use App\Observers\LiveChatObserver;
use App\Observers\NotificationObserver;
use App\Observers\UserTaggedObserver;

use App\Models\User;
use App\Models\Notification;
use App\Models\UserTagged;
use App\Models\Rating;
use App\Models\Comment;
use App\Observers\RatingObserver;
use App\Observers\CommentObserver;
use App\Models\LiveChat;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Request $request): void
    {
        // Register model observers for cache invalidation
        Story::observe(StoryObserver::class);
        Chapter::observe(ChapterObserver::class);
        Rating::observe(RatingObserver::class);
        Comment::observe(CommentObserver::class);
        LiveChat::observe(LiveChatObserver::class);
        Donation::observe(DonationObserver::class);
        User::observe(UserObserver::class);
        Notification::observe(NotificationObserver::class);
        UserTagged::observe(UserTaggedObserver::class);
        Category::observe(CategoryObserver::class);
        //Setting::observe(SettingObserver::class);
        BanIp::observe(BannedIpObserver::class);
        //Status::observe(StatusObserver::class);

        // Calculate stats with caching
        $stats = Cache::remember('app:stats', now()->addMinutes(10), function () {
            try {
                return [
                    'total_chapters' => Chapter::selectRaw('story_id, COUNT(*) as count')
                        ->groupBy('story_id')
                        ->pluck('count', 'story_id'),
                    'total_views' => Chapter::whereNotNull('views')
                        ->selectRaw('story_id, SUM(views) as total_views')
                        ->groupBy('story_id')
                        ->pluck('total_views', 'story_id')
                        ->toArray(),
                    'ratings' => [
                        'count' => User::whereNotNull('rating')->count(),
                        'average' => number_format(User::whereNotNull('rating')->avg('rating') ?? 0, 1)
                    ]
                ];
            } catch (\Exception $e) {
                // Return empty stats if tables don't exist yet
                return [
                    'total_chapters' => collect(),
                    'total_views' => [],
                    'ratings' => [
                        'count' => 0,
                        'average' => '0.0'
                    ]
                ];
            }
        });

        // Get story status with caching
        // $status = Cache::remember('app:status', now()->addMinutes(10), function () {
        //     return Status::first();
        // });

        Paginator::useBootstrap();
        if ($request->server->get('HTTPS') === 'on') {
            URL::forceScheme('https');
        }

        if (in_array(config('app.env', 'local'), ['production', 'staging'])) {
            URL::forceScheme('https');
        }
        Carbon::setLocale('vi');
        setlocale(LC_TIME, 'vi_VN.UTF-8');

        View::composer('Frontend.layouts.default', LayoutComposer::class);
        View::composer('Frontend.home', HomeComposer::class);
        View::composer('Frontend.category', LayoutComposer::class);
        View::composer('Frontend.follow_chapter_count', LayoutComposer::class);
        View::composer('Frontend.story', HomeComposer::class);
        View::composer('Frontend.chapter', ChapterComposer::class);
        // Share with all views
        View::share('stats', $stats);
       // View::share('status', $status);
    }
}
