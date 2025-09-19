<?php

namespace App\Models;

use App\Models\User;
use App\Models\ServicePricing;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class Service extends Model
{
    //
    use HasFactory, SoftDeletes;
    use  Auditable;
    use AutoHtmlDecode;

    protected $fillable = [
        'name',
        'price',
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
        'meta_keywords',
        'image_url',
        'gallery_images',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'gallery_images' => 'array'
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class);
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
