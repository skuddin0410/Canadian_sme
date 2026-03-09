<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Event;
use App\Models\Session;
use App\Models\PollQuestion;
use App\Models\PollAnswer;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_session_id',
        'title',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function eventSession()
    {
        return $this->belongsTo(Session::class);
    }

    public function questions()
    {
        return $this->hasMany(PollQuestion::class);
    }
    public function answers()
    {
        return $this->hasManyThrough(
            PollAnswer::class,
            PollQuestion::class,
            'poll_id',
            'poll_question_id'
        );
    }
}
