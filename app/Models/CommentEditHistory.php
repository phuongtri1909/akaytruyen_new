<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentEditHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'old_content',
        'new_content',
        'edited_by',
        'edited_at',
        'edit_reason'
    ];

    protected $casts = [
        'edited_at' => 'datetime'
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
