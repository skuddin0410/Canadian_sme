<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingApartCard extends Model
{
    protected $fillable = [
        'heading',
        'description',
        'text',
        'icon',
        'status',
        'order_by',
    ];

    public function cardIcon()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'landing_apart_cards')
            ->where('file_type', 'icon')
            ->whereNotNull('file_name');
    }
}
