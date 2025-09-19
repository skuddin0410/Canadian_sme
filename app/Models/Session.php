<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class Session extends Model
{   
    use  Auditable;
    use AutoHtmlDecode;
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
        return $this->belongsToMany(User::class, 'session_speakers', 'session_id', 'speaker_id')->withTimestamps();
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

    public function getStartsTimeInAttribute()
    {
        if (!$this->start_time) {
            return null;
        }
        $now = now();
        $target = $this->start_time; 
        $diff = $now->diff($target);
        $dd = sprintf('%02d', $diff->d);
        $hh = sprintf('%02d', $diff->h);
        $mm = sprintf('%02d', $diff->i);
        $ss = sprintf('%02d', $diff->s); 
        return [
            'direction' => $diff->invert ? 'since' : 'in',
            'days'      => $dd,
            'hours'     => $hh,
            'minutes'   => $mm,
            'seconds'   => $ss,
            'year'   => $target->year,
            'month'   => $target->month,
            'formatted' => sprintf('%dd %02dh %02dm %02ds', $diff->d, $diff->h, $diff->i, $diff->s),
        ];
    }
    
    public function agendas()
    {
        return $this->hasMany(UserAgenda::class);
    }

    public function favorites()
    {
        return $this->hasMany(FavoriteSession::class);
    }
 

    protected $appends = ['starts_in','starts_time_in'];
}