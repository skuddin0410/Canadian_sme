<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'tags',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function photo()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'pages')
            ->where('file_type', 'photo')
            ->whereNotNull('file_name');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
