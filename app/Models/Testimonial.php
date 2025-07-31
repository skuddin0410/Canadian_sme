<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'rating',
        'message',
        'order',
        'status',
    ];

    public function photo()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'testimonials')
            ->where('file_type', 'photo')
            ->whereNotNull('file_name');
    }
}
