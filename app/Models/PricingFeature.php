<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingFeature extends Model
{
    use HasFactory;

    protected $table = 'pricing_features';

    protected $fillable = [
        'name',
        'order_by',
        'status',
    ];

    public function values()
    {
        return $this->hasMany(PricingFeatureValue::class, 'feature_id');
    }

    /**
     * Get value for a specific plan.
     */
    public function getValueForPlan($planId)
    {
        return $this->values()->where('pricing_id', $planId)->first()?->value ?? 0;
    }
}
