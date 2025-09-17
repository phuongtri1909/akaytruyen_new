<?php

namespace App\Models;

use App\Traits\LogsOldValues;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class Story extends Model
{
    use HasFactory, LogsOldValues;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const FULL = 1;
    const IS_NEW = 1;
    const IS_HOT = 1;
    const IS_VIP = 1;

    protected $fillable = [
        'slug',
        'image',
        'name',
        'desc',
        'author_id',
        'status',
        'is_full',
        'is_new',
        'is_hot',
        'is_vip'
    ];

    // Accessor để tương thích với các field name khác
    public function getTitleAttribute()
    {
        return $this->name;
    }

    public function getDescriptionAttribute()
    {
        return $this->desc;
    }

    public function getActiveAttribute()
    {
        return $this->status ? 'active' : 'inactive';
    }

    protected $table = 'stories';

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_stories', 'story_id', 'category_id')
            ->withTimestamps();
    }

    public function chapters()
    {
        $query = $this->hasMany(Chapter::class, 'story_id');
        
        $user = Auth::user();
        if ($user) {
            if (($user->userBan && $user->userBan->read) || 
                ($this->is_vip && !$user->can('xem_chuong_truyen_vip'))) {
                
                $columns = Schema::getColumnListing('chapters');
                $selectColumns = [];
                
                foreach ($columns as $column) {
                    if ($column === 'content') {
                        $selectColumns[] = DB::raw('NULL as content');
                    } else {
                        $selectColumns[] = $column;
                    }
                }
                
                $query->select($selectColumns);
            }
        }
        
        return $query;
    }

    public function latestChapter()
    {
        return $this->hasOne(Chapter::class, 'story_id')->latestOfMany();
    }

    public function getChapterLastAttribute()
    {
        $chapter = $this->getRelationValue('latestChapter');
        
        $user = Auth::user();
        if ($chapter && $user) {
            if (($user->userBan && $user->userBan->read) || 
                ($this->is_vip && !$user->can('xem_chuong_truyen_vip'))) {
                $chapter->setAttribute('content', null);
            }
        }
        
        return $chapter;
    }

    public function getLatestChapter()
    {
        return $this->chapters()->latest('id')->first();
    }

    public function donates()
    {
        return $this->hasMany(Donate::class, 'story_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'story_id');
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('assets/frontend/images/default-story.jpg');
        }

        if (str_starts_with($this->image, '/images/stories/')) {
            return asset($this->image);
        }

        return asset('storage/' . $this->image);
    }

    public function scopeWithImage($query)
    {
        return $query->whereNotNull('image');
    }

    public function scopeWithoutImage($query)
    {
        return $query->whereNull('image');
    }
}
