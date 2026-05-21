<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAndEntityLink extends Model
{
    protected $table = 'event_and_entity_link';

    protected $fillable = [
        'event_id',
        'entity_type',
        'entity_id',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
