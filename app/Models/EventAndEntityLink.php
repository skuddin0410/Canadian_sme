<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAndEntityLink extends Model
{
    protected $table = 'event_and_entity_link';

    protected $fillable = [
        'event_id',
        'entity_type',
        'entity_id',
    ];

    // You can define any necessary relationships here as well, e.g., 
    // if you need to fetch related `event` data, you can use:
    // public function event()
    // {
    //     return $this->belongsTo(Event::class, 'event_id');
    // }
}
