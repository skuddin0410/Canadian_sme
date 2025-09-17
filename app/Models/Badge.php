<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name', 
        'designation',
        'logo_path',
        'qr_code_data',
        'qr_code_path',
        'badge_path',
        'selected_fields'
    ];

    protected $casts = [
        'selected_fields' => 'array'
    ];
}