<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingEventType extends Model
{
    protected $fillable = [
        'heading',
        'text',
        'image',
        'status',
        'order',
    ];

    public function typeImage()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'landing_event_types')
            ->where('file_type', 'type_image')
            ->whereNotNull('file_name');
    }
}
