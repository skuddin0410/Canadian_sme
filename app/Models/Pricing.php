<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    protected $table = 'pricing';

    protected $fillable = [
        'name',
        'amount',
        'description',
        'attendee_count',
        'timespan',
        'mostpopular',
        'event_no',
        'status',
        'order_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'attendee_count' => 'integer',
        'timespan' => 'integer',
        'mostpopular' => 'boolean',
        'event_no' => 'integer',
        'status' => 'boolean',
        'order_by' => 'integer',
    ];
}
