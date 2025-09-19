<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class Track extends Model
{
    use HasFactory;
    use  Auditable;
    use AutoHtmlDecode;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
    ];

    /**
     * Automatically generate slug when creating/updating if not set
     */
    protected static function booted()
    {
        static::creating(function ($track) {
            if (empty($track->slug)) {
                $track->slug = Str::slug($track->name);
            }
        });

        static::updating(function ($track) {
            if (empty($track->slug)) {
                $track->slug = Str::slug($track->name);
            }
        });
    }
}
