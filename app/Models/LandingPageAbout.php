<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageAbout extends Model
{
    protected $fillable = [
        'heading',
        'sub_heading',
        'description',
        'desc_points',
        'button_text',
        'button_link',
        'banner_button_link',
        'exp_year',
        'exp_text',
        'bg_banner',
        'banner_image',
        'front_image',
        'banner_button_image',
        'exp_image',
    ];

    /**
     * Relationships with Drive model for images
     */
    public function bgBanner()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'landing_page_abouts')
            ->where('file_type', 'bg_banner')
            ->whereNotNull('file_name');
    }

    public function bannerImage()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'landing_page_abouts')
            ->where('file_type', 'banner_image')
            ->whereNotNull('file_name');
    }

    public function frontImage()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'landing_page_abouts')
            ->where('file_type', 'front_image')
            ->whereNotNull('file_name');
    }

    public function bannerButtonImage()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'landing_page_abouts')
            ->where('file_type', 'banner_button_image')
            ->whereNotNull('file_name');
    }

    public function expImage()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'landing_page_abouts')
            ->where('file_type', 'exp_image')
            ->whereNotNull('file_name');
    }
}
