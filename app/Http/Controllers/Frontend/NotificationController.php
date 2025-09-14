<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend;
use Illuminate\Http\Request;
use App\Http\Controllers\Frontend\Controller;
use App\Models\Chapter;
use App\Models\Notification;
use App\Models\Story;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\UserTagged;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $user = auth()->user(); // Kiểm tra người dùng

        if (!$user) {
            return response()->json(['error' => 'Người dùng chưa đăng nhập'], 401);
        }


        $cacheTtl = now()->addSeconds(60);

        $notifications = Cache::remember("notifications:unread:user:{$user->id}", $cacheTtl, function () use ($user) {
            return DB::table('notifications')
                ->leftJoin('stories', 'notifications.story_id', '=', 'stories.id')
                ->leftJoin('chapters', 'notifications.chapter_id', '=', 'chapters.id')
                ->select(
                    'notifications.id',
                    'notifications.message',
                    'notifications.created_at',
                    'notifications.is_read', // Lấy trạng thái đọc
                    'stories.name as story_title',
                    'stories.slug as story_slug',
                    'chapters.chapter as chapter_number',
                    'chapters.name as chapter_title',
                    'chapters.slug as chapter_slug'
                )
                ->where('notifications.user_id', $user->id)
                ->where('notifications.created_at', '>=', now()->subDay())
                ->where('notifications.is_read', 0) // Chỉ lấy thông báo chưa đọc
                ->orderBy('notifications.created_at', 'desc')
                ->get();
        });

        $taggedNotifications = Cache::remember("notifications:tagged:user:{$user->id}", $cacheTtl, function () use ($user) {
            return DB::table('user_taggeds')
                ->leftJoin('comments', 'user_taggeds.comment_id', '=', 'comments.id')
                ->leftJoin('users', 'user_taggeds.tagged_by', '=', 'users.id')
                ->leftJoin('chapters', 'user_taggeds.chapter_id', '=', 'chapters.id') // So sánh trực tiếp chapter_id với chapters.id
                ->leftJoin('stories', 'chapters.story_id', '=', 'stories.id')
                ->select(
                    'user_taggeds.id',
                    'user_taggeds.created_at',
                    'users.name as tagger_name', // Tên người tag
                    'comments.comment as comment_text', // Nội dung comment
                    'stories.name as story_title',
                    'stories.slug as story_slug',
                    'chapters.chapter as chapter_number',
                    'chapters.name as chapter_title',
                    'chapters.slug as chapter_slug',
                    'comments.id as comment_id',
                )
                ->where('user_taggeds.user_id', $user->id)
                ->get();
        });


        // Trả về cả thông báo chương mới và thông báo bị tag
        return response()->json([
            'notifications' => $notifications,
            'tagged_notifications' => $taggedNotifications
        ]);

        // return response()->json($notifications);
    }


    public function markAsRead(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Người dùng chưa đăng nhập'], 401);
            }

            // Cập nhật thông báo của người dùng hiện tại thành đã đọc
            DB::table('notifications')
                ->where('user_id', $user->id)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);

            // Invalidate per-user notification cache
            Cache::forget("notifications:unread:user:{$user->id}");
            Cache::forget("notifications:tagged:user:{$user->id}");

            return response()->json(['message' => 'Thông báo đã được đánh dấu là đã đọc']);
        } catch (\Exception $e) {
            Log::error('Lỗi khi đánh dấu thông báo là đã đọc: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
        }
    }



    public function deleteTaggedNotification($notificationId)
    {
        // Ensure the user is authenticated
        $user = auth()->user();

        // Find the notification by its ID and ensure it's associated with the current user
        $notification = UserTagged::where('id', $notificationId)
            ->where('user_id', $user->id)
            ->first();

        if ($notification) {
            $notification->delete();
            // Invalidate per-user cache for tagged notifications
            Cache::forget("notifications:tagged:user:{$user->id}");
            return response()->json(['status' => 'success', 'message' => 'Notification deleted.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Notification not found or not yours to delete.']);
    }
}
