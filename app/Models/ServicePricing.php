<?php

namespace App\Models;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServicePricing extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'service_id',
        'tier_name',
        'price',
        'currency',
        'billing_period',
        'features',
        'is_quote_based',
        'is_popular',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'is_quote_based' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }
}
