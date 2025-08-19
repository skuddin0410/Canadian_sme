<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id', 'category_id', 'name', 'slug', 'description',
        'base_price', 'total_quantity', 'available_quantity',
        'min_quantity_per_order', 'max_quantity_per_order',
        'is_active', 'requires_approval', 'access_permissions',
        'sale_start_date', 'sale_end_date', 'sort_order'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
        'access_permissions' => 'array',
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime'
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    // public function pricingRules()
    // {
    //     return $this->hasMany(TicketPricingRule::class);
    // }

    public function inventoryLogs()
    {
        return $this->hasMany(TicketInventoryLog::class);
    }

    public function eventTickets()
    {
        return $this->hasMany(EventTicket::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_quantity', '>', 0)
                    ->where(function($q) {
                        $q->whereNull('sale_start_date')
                          ->orWhere('sale_start_date', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('sale_end_date')
                          ->orWhere('sale_end_date', '>=', now());
                    });
    }

    // Methods
    public function getCurrentPrice($quantity = 1, $promoCode = null)
    {
        $basePrice = $this->base_price;
        $applicableRules = $this->getApplicablePricingRules($quantity, $promoCode);
        
        if ($applicableRules->isNotEmpty()) {
            $bestRule = $applicableRules->sortBy('price')->first();
            return $bestRule->price;
        }
        
        return $basePrice;
    }

    public function getApplicablePricingRules($quantity = 1, $promoCode = null)
    {
        return $this->pricingRules()
            ->active()
            ->where(function($query) use ($quantity, $promoCode) {
                $query->where(function($q) use ($quantity) {
                    // Group discount rules
                    $q->where('type', 'group')
                      ->where('min_quantity', '<=', $quantity)
                      ->where(function($subQ) use ($quantity) {
                          $subQ->whereNull('max_quantity')
                               ->orWhere('max_quantity', '>=', $quantity);
                      });
                })
                ->orWhere(function($q) {
                    // Time-based rules (early bird, late bird)
                    $q->whereIn('type', ['early_bird', 'late_bird'])
                      ->where(function($subQ) {
                          $subQ->whereNull('start_date')
                               ->orWhere('start_date', '<=', now());
                      })
                      ->where(function($subQ) {
                          $subQ->whereNull('end_date')
                               ->orWhere('end_date', '>=', now());
                      });
                })
                ->when($promoCode, function($q) use ($promoCode) {
                    $q->orWhere(function($subQ) use ($promoCode) {
                        $subQ->where('type', 'promo_code')
                             ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(conditions, '$.code')) = ?", [$promoCode]);
                    });
                });
            })
            ->get();
    }

    public function reserveQuantity($quantity)
    {
        if ($this->available_quantity >= $quantity) {
            $this->decrement('available_quantity', $quantity);
            $this->logInventoryChange('reserve', $quantity, 'Reserved for booking');
            return true;
        }
        return false;
    }

    public function releaseQuantity($quantity)
    {
        $this->increment('available_quantity', $quantity);
        $this->logInventoryChange('release', $quantity, 'Released from reservation');
    }

    private function logInventoryChange($action, $quantity, $reason = null)
    {
        $this->inventoryLogs()->create([
            'action' => $action,
            'quantity' => $quantity,
            'previous_quantity' => $this->getOriginal('available_quantity'),
            'new_quantity' => $this->available_quantity,
            'reason' => $reason,
            'user_id' => auth()->id()
        ]);
    }
}