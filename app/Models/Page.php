<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class Page extends Model
{   
    use  Auditable;
    use AutoHtmlDecode;
    protected $fillable = [
        'name',
        'slug',
        'category',
        'tags',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'start_date',
        'end_date'
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
