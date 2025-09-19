<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class EmailTemplate extends Model
{
    use HasFactory;
    use  Auditable;
    use AutoHtmlDecode;

    protected $fillable = [
        'template_name',
        'subject',
        'type',
        'message',
    ];
}
