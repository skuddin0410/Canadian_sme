<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewBadge extends Model
{
    use HasFactory;

    protected $table = 'new_badges';

    protected $fillable = [
        'badge_name',
        'target',
        'printer',
        'width',
        'height',
    ];
}
