<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    public function getContentAttribute($value)
    {
        $user = Auth::user();
        
        if ($user && $user->userBan && $user->userBan->read) {
            return null;
        }
        
        if ($user && $this->story && $this->story->is_vip && !$user->can('xem_chuong_truyen_vip')) {
            return null;
        }
        
        return $value;
    }
    
    public function story() {
        return $this->belongsTo(Story::class, 'story_id', 'id');
    }
}
