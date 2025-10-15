<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageSetting extends Model
{
    use HasFactory;

    // Define which fields are fillable (i.e., can be mass-assigned)
    protected $fillable = ['title', 'date', 'location', 'website'];
}

