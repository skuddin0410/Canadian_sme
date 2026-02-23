<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageLogo extends Model
{
    protected $fillable = [
        'title',
        'status',
        'order_by',
        'image',
    ];

    public function logoImage()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'landing_page_logos')
            ->where('file_type', 'logo')
            ->whereNotNull('file_name');
    }
}
