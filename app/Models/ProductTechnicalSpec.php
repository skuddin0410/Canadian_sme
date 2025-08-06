<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductTechnicalSpec extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'product_id',
        'spec_name',
        'spec_value',
        'spec_unit',
        'spec_category',
        'is_important',
        'sort_order'
    ];

    protected $casts = [
        'is_important' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('spec_category', $category);
    }
}
