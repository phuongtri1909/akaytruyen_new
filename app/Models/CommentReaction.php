<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReaction extends Model
{
    use HasFactory;
    protected $fillable = ['comment_id', 'user_id', 'type'];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
    public function reactions()
    {
        return $this->hasMany(CommentReaction::class);
    }

    public function likes()
    {
        return $this->reactions()->where('type', 'like');
    }

    public function dislikes()
    {
        return $this->reactions()->where('type', 'dislike');
    }
    public function hahas()
    {
        return $this->reactions()->where('type', 'haha');
    }

    public function tyms()
    {
        return $this->reactions()->where('type', 'tym');
    }

    public function angrys()
    {
        return $this->reactions()->where('type', 'angry');
    }
    public function sads()
    {
        return $this->reactions()->where('type', 'sad');
    }
}
