<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/admin.php';

use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Frontend\Auth\AuthController;
use App\Http\Controllers\Frontend\{
    CategoryController,
    ChapterController,
    HomeController,
    StoryController,
    UserController,
    CommentController,
    CommentReactionController,
    CommentEditController,
    LiveChatController,
    NotificationController,
    SitemapController,
    DonationController,
    PageController
};
use App\Http\Controllers\Frontend\Auth\GoogleController;
use App\Http\Controllers\Frontend\ChatController;
use App\Http\Controllers\Frontend\RatingController;
use Livewire\Livewire;
use App\Http\Livewire\LiveChatSection;

use App\Http\Controllers\Frontend\DownloadController;

Route::get('akay/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "Cache is cleared";
})->name('clear.cache');

Route::get('/ads.txt', function () {
    return redirect('https://srv.adstxtmanager.com/19390/akaytruyen.com', 301);
});

Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/content-rules', [PageController::class, 'contentRules'])->name('content-rules');
Route::get('/confidental', [PageController::class, 'confidental'])->name('confidental');

// Sitemap Routes
Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('sitemap-categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories.alt');
Route::get('sitemap-stories.xml', [SitemapController::class, 'stories'])->name('sitemap.stories.alt');
Route::get('sitemap-chapters.xml', [SitemapController::class, 'chapters'])->name('sitemap.chapters.alt');
Route::get('sitemap-main-pages.xml', [SitemapController::class, 'mainPages'])->name('sitemap.main.pages');

// Guest routes
Route::middleware(['guest'])->group(function () {
    Route::view('/login', 'Frontend.auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('user.login');

    Route::view('/register', 'Frontend.auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::view('/forgot-password', 'Frontend.auth.forgot-password')->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.password');

    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::middleware(['ban:login'])->group(function () {
    // Public routes
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/the-loai/{slug}', [CategoryController::class, 'index'])->name('category');
    Route::get('/truyen/{slug}', [StoryController::class, 'index'])->name('story');
    Route::post('/truyen/{story}/toggle-vip', [StoryController::class, 'toggleVip'])->name('story.toggle-vip')->middleware('auth');

    Route::get('/{slugStory}/{slugChapter}', [ChapterController::class, 'index'])->name('chapter');
    
    Route::middleware(['ban:read'])->group(function () {
        // ajax search chapters
        Route::get('/truyen/{slug}/search-chapters', [HomeController::class, 'searchChapters'])->name('chapters.search');

        Route::post('/ajax/get-chapters', [ChapterController::class, 'getChapters'])->name('get.chapters');
    });
    // Donation routes for stories
    Route::get('/truyen/{storySlug}/donations', [DonationController::class, 'getStoryDonations'])->name('story.donations');

    Route::get('/tim-kiem', [HomeController::class, 'mainSearchStory'])->name('main.search.story');
    Route::get('/phan-loai-theo-chuong', [StoryController::class, 'followChaptersCount'])->name('get.list.story.with.chapters.count');

    // Route xem lịch sử edit comment (không cần đăng nhập)
    Route::get('/comments/{comment}/edit-history', [CommentEditController::class, 'getEditHistory'])->name('comments.edit.history');

    // Ajax Routes
    Route::post('/get-list-story-hot', [HomeController::class, 'getListStoryHot'])->name('get.list.story.hot');

    Route::post('/ajax/search-story', [HomeController::class, 'searchStory'])->name('search.story');


    Livewire::component('live-chat-section', LiveChatSection::class);


    Route::middleware(['auth'])->group(function () {
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/notifications', [NotificationController::class, 'getNotifications']);
        Route::post('/notifications/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAsRead']);

        Route::delete('/delete-tagged-notification/{notificationId}', [NotificationController::class, 'deleteTaggedNotification']);

        // Saved chapters routes
        Route::post('/save-reading-progress', [ChapterController::class, 'saveReadingProgress'])->name('save.reading.progress');
        Route::get('/saved-chapters', [ChapterController::class, 'getSavedChapters'])->name('get.saved.chapters');

        // User profile
        Route::get('profile', [UserController::class, 'userProfile'])->name('profile');
        Route::post('update-profile/update-name-or-phone', [UserController::class, 'updateNameOrPhone'])->name('update.name.or.phone');
        Route::post('update-avatar', [UserController::class, 'updateAvatar'])->name('update.avatar');
        Route::post('update-password', [UserController::class, 'updatePassword'])->name('update.password');

        Route::middleware(['ban:comment'])->group(function () {

            Route::get('/search-users', [UserController::class, 'searchUser'])->name('user.search');

            Route::post('/comments/{comment}/react', [CommentReactionController::class, 'react'])->name('comments.react');

            // Routes cho edit comment
            Route::get('/comments/{comment}/edit-form', [CommentEditController::class, 'getEditForm'])->name('comments.edit.form');
            Route::post('/comments/{comment}/edit', [CommentEditController::class, 'edit'])->name('comments.edit');

            // Route ghim comment (pin/unpin)
            Route::post('/comments/{comment}/pin', [CommentController::class, 'togglePin'])->name('comments.pin');

            
            // frontend delete comment
            Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

            Route::post('comment/store', [CommentController::class, 'storeClient'])->name('comment.store.client');
        });

        Route::middleware(['ban:rate'])->group(function () {
            Route::post('/ratings', [RatingController::class, 'storeClient'])->name('ratings.store');
        });

        Route::middleware(['ban:read'])->group(function () {
            Route::get('/{slugStory}/{slugChapter}/download-epub', [DownloadController::class, 'generateEpub'])
                ->name('download.epub');
        });
    });
});
