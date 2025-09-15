<?php

namespace App\Models;

use App\Models\CommentReaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'reply_id',
        'user_id',
        'comment',
        'level',
        'is_pinned',
        'pinned_at',
        'is_edited',
        'edited_at',
        'edited_by',
        'edit_count'
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'edit_count' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comment) {
            if ($comment->reply_id) {
                $parentComment = Comment::find($comment->reply_id);
                $comment->level = $parentComment ? $parentComment->level + 1 : 0;
            }
        });
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'reply_id')->where('level', '<', 3);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'reply_id');
    }

    public function reactions()
    {
        return $this->hasMany(CommentReaction::class);
    }

    public function editHistories()
    {
        return $this->hasMany(CommentEditHistory::class)->orderBy('edited_at', 'desc');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function likes()
    {
        return $this->hasMany(CommentReaction::class)->where('type', 'like');
    }

    public function dislikes()
    {
        return $this->hasMany(CommentReaction::class)->where('type', 'dislike');
    }
    public function hahas()
    {
        return $this->hasMany(CommentReaction::class)->where('type', 'haha');
    }

    public function tyms()
    {
        return $this->hasMany(CommentReaction::class)->where('type', 'tym');
    }
    public function angrys()
    {
        return $this->hasMany(CommentReaction::class)->where('type', 'angry');
    }
    public function sads()
    {
        return $this->hasMany(CommentReaction::class)->where('type', 'sad');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true)->orderBy('pinned_at', 'desc');
    }
}
