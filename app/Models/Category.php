<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{   
    use  Auditable;
    use AutoHtmlDecode;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'slug',
        'type',
        'order',
        'color'
    ];

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category', 'id');
    }
}
