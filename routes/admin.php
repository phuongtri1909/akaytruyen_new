<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\DonateController;
use App\Http\Controllers\Admin\DonationController;

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


    // Role Management
    Route::resource('roles', RoleController::class)->except('show');

    // Permission Management
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('permissions/get-roles', [PermissionController::class, 'getRoles'])->name('permissions.get-roles');
    Route::post('permissions/assign-roles', [PermissionController::class, 'assignRoles'])->name('permissions.assign-roles');

    // User Management
    Route::resource('users', UserController::class);

    // Category Management
    Route::resource('categories', CategoryController::class);

    // Story Management
    Route::resource('stories', StoryController::class);
    Route::post('stories/{story}/toggle-status', [StoryController::class, 'toggleStatus'])->name('stories.toggle-status');

    // Chapter Management
    Route::resource('chapters', ChapterController::class)->except('index', 'show');
    Route::post('chapters/{chapter}/toggle-status', [ChapterController::class, 'toggleStatus'])->name('chapters.toggle-status');

    // Donate Management
    Route::get('donate/{storyId}', [DonateController::class, 'index'])->name('donate.index');
    Route::post('donate/{storyId}', [DonateController::class, 'store'])->name('donate.store');
    Route::put('donate/{donateId}', [DonateController::class, 'update'])->name('donate.update');
    Route::delete('donate/{donateId}', [DonateController::class, 'destroy'])->name('donate.destroy');

    // Donation Management
    Route::get('donations/{storyId}', [DonationController::class, 'index'])->name('donations.index');
    Route::post('donations/{storyId}', [DonationController::class, 'store'])->name('donations.store');
    Route::put('donations/{donationId}', [DonationController::class, 'update'])->name('donations.update');
    Route::delete('donations/{donationId}', [DonationController::class, 'destroy'])->name('donations.destroy');

    // Ban/Unban routes
    Route::get('users/{user}/ban-info', [UserController::class, 'getBanInfo'])->name('users.ban-info');
    Route::post('users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
    Route::post('users/ban-ip', [UserController::class, 'banIp'])->name('users.ban-ip');
    Route::delete('users/ban-ip/{banIp}', [UserController::class, 'unbanIp'])->name('users.unban-ip');

    // Settings routes
    Route::get('setting', [SettingController::class, 'index'])->name('setting.index');

    Route::put('setting/smtp', [SettingController::class, 'updateSMTP'])->name('setting.update.smtp');

    Route::put('setting/google', [SettingController::class, 'updateGoogle'])->name('setting.update.google');
});
