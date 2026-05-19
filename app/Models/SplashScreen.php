<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class SplashScreen extends Model
{
    use Auditable;

    protected $fillable = [
        'event_id',
        'created_by',
        'ios_iphone_image',
        'ios_ipad_image',
        'android_hdpi_image',
        'android_mdpi_image',
        'android_xhdpi_image',
        'android_xxhdpi_image',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function iosIphone()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'splash_screens')
            ->where('file_type', 'ios_iphone_image');
    }

    public function iosIpad()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'splash_screens')
            ->where('file_type', 'ios_ipad_image');
    }

    public function androidHdpi()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'splash_screens')
            ->where('file_type', 'android_hdpi_image');
    }

    public function androidMdpi()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'splash_screens')
            ->where('file_type', 'android_mdpi_image');
    }

    public function androidXhdpi()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'splash_screens')
            ->where('file_type', 'android_xhdpi_image');
    }

    public function androidXxhdpi()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'splash_screens')
            ->where('file_type', 'android_xxhdpi_image');
    }
}
