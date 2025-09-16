<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SeoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_key',
        'title',
        'description',
        'keywords',
        'thumbnail',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Scope for active SEO settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get SEO setting by page key
     */
    public static function getByPageKey($pageKey)
    {
        return static::where('page_key', $pageKey)->active()->first();
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        return asset('assets/images/dev/Thumbnail.png'); 
    }

    /**
     * Get all page keys
     */
    public static function getPageKeys()
    {
        return [
            'home' => 'Trang chủ',
            'stories' => 'Danh sách truyện',
            'story_detail' => 'Chi tiết truyện',
            'chapter_detail' => 'Chi tiết chương',
            'categories' => 'Danh mục',
            'search' => 'Tìm kiếm',
            'contact' => 'Liên hệ',
            'about' => 'Giới thiệu'
        ];
    }
}
