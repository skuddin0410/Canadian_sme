<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Poll;
use App\Models\PollAnswer;

class PollQuestion extends Model
{
     use HasFactory;

    protected $fillable = [
        'poll_id',
        'question',
        'type',
        'rating_scale',
    ];

    protected $casts = [
        'rating_scale' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function answers()
    {
        return $this->hasMany(PollAnswer::class);
    }
}
