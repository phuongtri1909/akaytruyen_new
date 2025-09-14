<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $table = 'donations';

    protected $fillable = ['name', 'amount', 'donated_at', 'story_id'];

    public $timestamps = true;

    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
