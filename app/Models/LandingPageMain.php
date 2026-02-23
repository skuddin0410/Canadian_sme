<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageMain extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'button_link',
        'image',
    ];

    public function mainImage()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'landing_page_mains')
            ->where('file_type', 'main_image')
            ->whereNotNull('file_name');
    }
}
