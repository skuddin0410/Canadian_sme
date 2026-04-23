<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GalleryItem extends Model
{
    protected $fillable = [
        'file_path',
        'file_name',
        'file_type',
        'added_by',
        'is_approved',
        'event_id',
    ];

    public function getFilePathAttribute($value)
    {
        if (empty($value)) {
            return asset('images/default.png');
        }

        // If it's already a full URL, return it
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Gallery items are stored on the 'public' disk (local)
        // as per EventGuideController@uploadGallery
        return asset('storage/' . $value);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
