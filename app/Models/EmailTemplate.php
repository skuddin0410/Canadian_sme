<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use HasFactory;
    use  Auditable;
    use AutoHtmlDecode;
    use SoftDeletes;
    protected $dates = ['deleted_at']; 
    protected $fillable = [
        'template_name',
        'subject',
        'type',
        'message',
    ];
}
