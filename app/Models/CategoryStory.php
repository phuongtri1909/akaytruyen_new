<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryStory extends Model
{
    protected $table = 'categories_stories';

    protected $fillable = ['category_id', 'story_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
