<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingFeatureValue extends Model
{
    use HasFactory;

    protected $table = 'pricing_feature_values';

    protected $fillable = [
        'feature_id',
        'pricing_id',
        'value',
    ];

    public function feature()
    {
        return $this->belongsTo(PricingFeature::class, 'feature_id');
    }

    public function plan()
    {
        return $this->belongsTo(Pricing::class, 'pricing_id');
    }
}
