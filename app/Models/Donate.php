<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donate extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_id',
        'bank_name',
        'donate_info',
        'image'
    ];

    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id');
    }
}
