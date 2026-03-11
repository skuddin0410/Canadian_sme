<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingCms extends Model
{
    use HasFactory;

    protected $table = 'pricing_cms';

    protected $fillable = [
        'main_heading',
        'main_description',
        'Feature_heading',
        'Feature_description',
    ];
}
