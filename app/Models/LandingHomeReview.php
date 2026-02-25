<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingHomeReview extends Model
{
    protected $fillable = [
        'customer_name',
        'slug',
        'description',
        'profile_image',
        'status',
        'order_by',
    ];

    public function profileImage()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'landing_home_reviews')
            ->where('file_type', 'profile_image')
            ->whereNotNull('file_name');
    }
}
