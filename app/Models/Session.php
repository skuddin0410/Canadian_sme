<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Session extends Model
{
    protected $table = "event_sessions";
    protected $fillable = [
        'event_id', 'booth_id', 'title', 'description',
        'start_time', 'end_time', 'status', 'type', 'capacity', 'metadata','keynote','demoes','panels','location','color','track'
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

    public function booth()
    {
        return $this->belongsTo(Booth::class, 'booth_id');
    }
    

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'session_speakers', 'session_id', 'user_id')->withTimestamps();
    }

    public function exhibitors(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'session_exhibitors', 'session_id', 'company_id')->withTimestamps();
    }


    public function sponsors(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'session_sponsors', 'session_id', 'company_id')->withTimestamps();
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'session_attendees', 'session_id', 'user_id')->withTimestamps();
    }

    public function tickets()
    {
         return $this->hasMany(EventTicket::class, 'session_id');
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

    public function photo()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'event_sessions')
            ->where('file_type', 'photo')
            ->whereNotNull('file_name');
    }

    public function getStartsInAttribute()
    {
        return now()->diffForHumans($this->start_time, [
            'parts' => 2,
        ]);
    }

    protected $appends = ['starts_in'];
}