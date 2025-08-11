<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Session extends Model
{
    protected $fillable = [
        'event_id', 'track_id', 'venue_id', 'title', 'description',
        'start_time', 'end_time', 'status', 'type', 'capacity', 'metadata'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'metadata' => 'array'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class, 'session_speakers')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function getDurationInMinutes()
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function isConflictingWith($otherSession)
    {
        return $this->start_time < $otherSession->end_time && 
               $this->end_time > $otherSession->start_time;
    }
}