<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_type_id', 'name', 'type', 'price', 'discount_amount',
        'discount_percentage', 'start_date', 'end_date', 'min_quantity',
        'max_quantity', 'usage_limit', 'usage_count', 'conditions', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'conditions' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    // Methods
    public function canBeUsed($quantity = 1)
    {
        if (!$this->is_active) return false;
        
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }
        
        if ($this->start_date && now()->lt($this->start_date)) return false;
        if ($this->end_date && now()->gt($this->end_date)) return false;
        
        if ($this->min_quantity && $quantity < $this->min_quantity) return false;
        if ($this->max_quantity && $quantity > $this->max_quantity) return false;
        
        return true;
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}