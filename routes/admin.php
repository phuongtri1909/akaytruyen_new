<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\StoryController;

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
