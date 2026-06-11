<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventWaitlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_type_id',
        'form_id',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'company',
        'designation',
        'registration_mode',
        'attendee_count',
        'coordinator_attending',
        'team_members',
        'request',
        'status',
        'notes',
        'joined_at',
    ];

    protected $casts = [
        'attendee_count' => 'integer',
        'coordinator_attending' => 'boolean',
        'team_members' => 'array',
        'request' => 'array',
        'joined_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
