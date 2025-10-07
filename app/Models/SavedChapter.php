<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'story_id', 
        'chapter_id',
        'scroll_position',
        'read_progress',
        'last_read_at'
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
        'read_progress' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}