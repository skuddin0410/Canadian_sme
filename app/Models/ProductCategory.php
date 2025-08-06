<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory extends Model
{
    //
      use HasFactory, SoftDeletes;
      protected $table = "products_categories";

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'is_active',
        'sort_order',
        'image_url'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
