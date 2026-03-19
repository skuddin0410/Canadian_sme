<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\User;
use App\Models\Pricing;

class Subscription extends Model
{
    protected $fillable = [
        'price_id',
        'user_id',
        'event_count',
        'attendee_count',
        'expired_at',
        'status'
    ];
    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function pricing()
    {
        return $this->belongsTo(Pricing::class, 'price_id');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            });
    }
}
