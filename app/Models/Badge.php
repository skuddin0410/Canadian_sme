<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class Badge extends Model
{
    use HasFactory;
    use  Auditable;
    use AutoHtmlDecode;

    protected $fillable = [
        'name',
        'company_name', 
        'designation',
        'logo_path',
        'qr_code_data',
        'qr_code_path',
        'badge_path',
        'selected_fields',
        'badge_name',
        'width',
        'height'
    ];

    protected $casts = [
        'selected_fields' => 'array'
    ];
}