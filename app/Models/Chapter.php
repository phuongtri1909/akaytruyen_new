<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Chapter extends Model
{
    use HasFactory;

    const IS_NEW = 1;

    const CONTENT_TYPE_PLAIN = 'plain';
    const CONTENT_TYPE_RICH = 'rich';

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'story_id',
        'name',
        'chapter',
        'content',
        'content_type',
        'status',
        'scheduled_publish_at',
        'published_at',
        'slug',
        'is_new',
        'views'
    ];

    protected $casts = [
        'chapter' => 'integer',
        'story_id' => 'integer',
        'is_new' => 'integer',
        'views' => 'integer',
        'scheduled_publish_at' => 'datetime',
        'published_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'updated_content_at'
    ];

    public function getContentAttribute($value)
    {
        $user = Auth::user();
        
        if ($user && $user->userBan && $user->userBan->read) {
            return null;
        }
        
        if ($user && $this->story && $this->story->is_vip && !$user->can('xem_chuong_truyen_vip')) {
            return null;
        }
        
        return $value;
    }
    
    public function story() {
        return $this->belongsTo(Story::class, 'story_id', 'id');
    }

    /**
     * Scope: Chỉ lấy chương đã xuất bản (để hiển thị cho người đọc)
     */
    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->where('status', Chapter::STATUS_PUBLISHED)
              ->orWhereNull('status');
        });
    }

    /**
     * Kiểm tra chương đã xuất bản chưa
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED || $this->status === null;
    }
}
