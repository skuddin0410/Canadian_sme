<?php

namespace App\Models;

use App\Models\User;
use App\Models\ServicePricing;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'capabilities',
        'deliverables',
        'category_id',
        'is_active',
        'sort_order',
        'duration',
        'meta_title',
        'meta_description',
        'image_url',
        'gallery_images',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'capabilities' => 'array',
        'deliverables' => 'array',
        'gallery_images' => 'array',
        'is_active' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function pricingTiers()
    {
        return $this->hasMany(ServicePricing::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
