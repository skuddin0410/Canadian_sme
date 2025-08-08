<?php

namespace App\Models;

use App\Models\User;
use App\Models\Company;
use App\Models\ProductPricing;
use App\Models\ProductCategory;
use App\Models\ProductTechnicalSpec;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    //
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'features',
        'benefits',
        'category_id',
        'user_id',        
        'company_id',     
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
        'image_url',
        'gallery_images',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        //'features' => 'array',
        //'benefits' => 'array',
        'gallery_images' => 'array',
        'is_active' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function company()  
    {
        return $this->belongsTo(Company::class);
    }

    public function user()  
    {
        return $this->belongsTo(User::class);
    }

    public function technicalSpecs()
    {
        return $this->hasMany(ProductTechnicalSpec::class);
    }

    public function pricingTiers()
    {
        return $this->hasMany(ProductPricing::class);
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
