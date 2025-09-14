<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    const IS_NEW = 1;

    protected $fillable = [
        'story_id',
        'name',
        'chapter',
        'content',
        'slug',
        'is_new',
        'views'
    ];

    protected $casts = [
        'chapter' => 'integer',
        'story_id' => 'integer',
        'is_new' => 'integer',
        'views' => 'integer'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'updated_content_at'
    ];
    
    public function story() {
        return $this->belongsTo(Story::class, 'story_id', 'id');
    }
}
