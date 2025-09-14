<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LiveChatReaction extends Model
{
    use HasFactory;

    protected $table = 'live_chat_reactions';
    protected $fillable = ['user_id', 'comment_id', 'type'];

    /**
     * Boot method để clear cache khi model thay đổi
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($reaction) {
            static::clearReactionCache($reaction->comment_id);
        });

        static::updated(function ($reaction) {
            static::clearReactionCache($reaction->comment_id);
        });

        static::deleted(function ($reaction) {
            static::clearReactionCache($reaction->comment_id);
        });
    }

    public function comment()
    {
        return $this->belongsTo(LiveChat::class, 'comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy reactions cho một comment với cache
     */
    public static function getCachedReactions($commentId)
    {
        $cacheKey = "comment_reactions_{$commentId}";

        return Cache::remember($cacheKey, 300, function () use ($commentId) {
            return static::where('comment_id', $commentId)
                ->with('user:id,name')
                ->get()
                ->groupBy('type');
        });
    }

    /**
     * Lấy số lượng reaction theo type cho một comment
     */
    public static function getReactionCounts($commentId)
    {
        $cacheKey = "comment_reaction_counts_{$commentId}";

        return Cache::remember($cacheKey, 300, function () use ($commentId) {
            return static::where('comment_id', $commentId)
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();
        });
    }

    /**
     * Kiểm tra user đã reaction chưa
     */
    public static function getUserReaction($userId, $commentId)
    {
        $cacheKey = "user_reaction_{$userId}_{$commentId}";

        return Cache::remember($cacheKey, 300, function () use ($userId, $commentId) {
            return static::where('user_id', $userId)
                ->where('comment_id', $commentId)
                ->first();
        });
    }

    /**
     * Clear cache cho reactions của một comment
     */
    public static function clearReactionCache($commentId)
    {
        Cache::forget("comment_reactions_{$commentId}");
        Cache::forget("comment_reaction_counts_{$commentId}");

        // Clear user reaction cache
        $reactions = static::where('comment_id', $commentId)->get();
        foreach ($reactions as $reaction) {
            Cache::forget("user_reaction_{$reaction->user_id}_{$commentId}");
        }
    }

    /**
     * Toggle reaction - thêm hoặc xóa reaction
     */
    public static function toggleReaction($userId, $commentId, $type)
    {
        $existingReaction = static::where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->first();

        if ($existingReaction) {
            if ($existingReaction->type === $type) {
                // Xóa reaction nếu cùng type
                $existingReaction->delete();
                return null;
            } else {
                // Update type nếu khác
                $existingReaction->update(['type' => $type]);
                return $existingReaction;
            }
        } else {
            // Tạo reaction mới
            return static::create([
                'user_id' => $userId,
                'comment_id' => $commentId,
                'type' => $type
            ]);
        }
    }
}
