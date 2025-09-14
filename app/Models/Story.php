<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Story extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const FULL = 1;
    const IS_NEW = 1;
    const IS_HOT = 1;

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
        'views'
    ];

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
        return $this->hasMany(Chapter::class, 'story_id');
    }

    public function latestChapter()
    {
        return $this->hasOne(Chapter::class, 'story_id')->latestOfMany();
    }

    public function getChapterLastAttribute()
    {
        return $this->getRelationValue('latestChapter');
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
