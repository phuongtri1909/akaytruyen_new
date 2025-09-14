<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTagged extends Model
{
    use HasFactory;
    protected $table = 'user_taggeds';
    protected $fillable = [
        'user_id',
        'comment_id',
        'tagged_by',
        'chapter_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taggedBy()
    {
        return $this->belongsTo(User::class, 'tagged_by');
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
