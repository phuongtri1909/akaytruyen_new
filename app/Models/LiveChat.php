<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class LiveChat extends Model
{
    use HasFactory;

    protected $table = 'live_chats';
    protected $fillable = ['user_id', 'content', 'parent_id', 'pinned'];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'pinned' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method để clear cache khi model thay đổi
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($liveChat) {
            static::clearCommentCache();
        });

        static::updated(function ($liveChat) {
            static::clearCommentCache();
        });

        static::deleted(function ($liveChat) {
            static::clearCommentCache();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Unknown User',
            'avatar' => null,
        ]);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(LiveChat::class, 'parent_id')->oldest();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LiveChat::class, 'parent_id');
    }


    /**
     * Scope để lấy comments chính (không phải reply)
     */
    public function scopeMainComments($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope để lấy comments đã ghim
     */
    public function scopePinned($query)
    {
        return $query->where('pinned', true);
    }

    /**
     * Scope để lấy comments với eager loading tối ưu
     */
    public function scopeWithOptimizedRelations($query)
    {
        return $query->with([
            'user:id,name,email,avatar',
            'user.ban:id,user_id,comment',
            'user.roles:id,name',
            'replies' => function ($query) {
                $query->select('id','user_id','content','parent_id','created_at')
                    ->orderBy('created_at')
                    ->limit(5)
                    ->with([
                        'user:id,name,email,avatar',
                        'user.ban:id,user_id,comment',
                        'user.roles:id,name'
                    ]);
            }
        ]);
    }

    /**
     * Lấy comments với cache
     */
    public static function getCachedComments($limit = 10)
    {
        $cacheKey = "comments_page_{$limit}";

        return Cache::remember($cacheKey, 300, function () use ($limit) {
            return static::select('id','user_id','content','pinned','created_at','parent_id')
                ->mainComments()
                ->withOptimizedRelations()
                ->orderByDesc('pinned')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Lấy tổng số comments chính với cache
     */
    public static function getCachedMainCommentCount()
    {
        return Cache::remember('total_main_comments', 300, function () {
            return static::mainComments()->count();
        });
    }

    /**
     * Clear tất cả cache liên quan đến comments
     */
    public static function clearCommentCache()
    {
        // Clear comment count cache
        Cache::forget('total_main_comments');

        // Clear tất cả comment page cache
        for ($i = 10; $i <= 100; $i += 10) {
            Cache::forget("comments_page_{$i}");
        }

        // Clear user cache nếu cần
        Cache::forget('users_with_roles');
    }
}
