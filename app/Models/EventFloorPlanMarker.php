<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventFloorPlanMarker extends Model
{
    protected $fillable = [
        'event_id',
        'booth_id',
        'company_id',
        'label',
        'x_percent',
        'y_percent',
        'width_percent',
        'height_percent',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'x_percent' => 'float',
        'y_percent' => 'float',
        'width_percent' => 'float',
        'height_percent' => 'float',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function booth()
    {
        return $this->belongsTo(Booth::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
