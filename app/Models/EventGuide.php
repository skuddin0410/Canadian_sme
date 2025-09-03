<?php

namespace App\Models;

use App\Models\Drive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'title',
        'type',
        'weblink',
        'doc',
    ];
    public function doc()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'event_guides')
            ->where('file_type', 'doc');
    }
    public function galleryImages()
{
    return $this->hasMany(Drive::class, 'table_id', 'id')
        ->where('table_type', 'event_guides')
        ->where('file_type', 'gallery')
        ->whereNotNull('file_name');
}

}
