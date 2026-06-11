<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class TicketType extends Model
{
    use HasFactory, SoftDeletes;
    use  Auditable;
    use AutoHtmlDecode;

    protected $fillable = [
        'event_id', 'category_id', 'name', 'slug', 'description',
        'base_price', 'total_quantity', 'available_quantity',
        'min_quantity_per_order', 'max_quantity_per_order',
        'is_group', 'group_size', 'discount_percentage',
        'is_earlybird', 'earlybird_amount', 'earlybird_quantity',
        'is_active', 'requires_approval', 'access_permissions',
        'sale_start_date', 'sale_end_date', 'sort_order', 'created_by'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'earlybird_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'is_group' => 'boolean',
        'is_earlybird' => 'boolean',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function ticketPurchases()
    {
        return $this->hasMany(TicketPurchase::class);
    }

    public function promoCodes()
    {
        return $this->hasMany(PromoCode::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'ticket_purchases', 'ticket_type_id', 'user_id')
            ->withTimestamps()
            ->distinct();
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

    public function isSaleOpen(): bool
    {
        if (!$this->is_active || $this->available_quantity <= 0) {
            return false;
        }

        if ($this->sale_start_date && $this->sale_start_date->isFuture()) {
            return false;
        }

        if ($this->sale_end_date && $this->sale_end_date->isPast()) {
            return false;
        }

        return true;
    }

    public function getRegistrationPricing(int $quantity = 1): array
    {
        $quantity = max($quantity, 1);
        $basePrice = round((float) $this->base_price, 2);
        $earlyBirdUnits = 0;
        $earlyBirdPrice = $this->is_earlybird && $this->earlybird_amount !== null
            ? round((float) $this->earlybird_amount, 2)
            : null;

        if ($earlyBirdPrice !== null && (int) $this->earlybird_quantity > 0) {
            $soldCount = max((int) $this->total_quantity - (int) $this->available_quantity, 0);
            $remainingEarlyBird = max((int) $this->earlybird_quantity - $soldCount, 0);
            $earlyBirdUnits = min($quantity, $remainingEarlyBird);
        }

        $regularUnits = max($quantity - $earlyBirdUnits, 0);
        $groupDiscountPercent = 0.0;

        if ($this->is_group && (int) $this->group_size > 1 && $quantity >= (int) $this->group_size) {
            $groupDiscountPercent = round((float) ($this->discount_percentage ?? 0), 2);
        }

        $regularUnitPrice = $basePrice;
        $earlyBirdUnitPrice = $earlyBirdPrice ?? $basePrice;

        if ($groupDiscountPercent > 0) {
            $multiplier = (100 - $groupDiscountPercent) / 100;
            $regularUnitPrice = round($regularUnitPrice * $multiplier, 2);
            $earlyBirdUnitPrice = round($earlyBirdUnitPrice * $multiplier, 2);
        }

        $perAttendeeAmounts = [];
        for ($i = 0; $i < $earlyBirdUnits; $i++) {
            $perAttendeeAmounts[] = $earlyBirdUnitPrice;
        }
        for ($i = 0; $i < $regularUnits; $i++) {
            $perAttendeeAmounts[] = $regularUnitPrice;
        }

        $subtotal = round(array_sum($perAttendeeAmounts), 2);
        $baseSubtotal = round($basePrice * $quantity, 2);

        return [
            'quantity' => $quantity,
            'base_price' => $basePrice,
            'base_subtotal' => $baseSubtotal,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'savings' => round($baseSubtotal - $subtotal, 2),
            'early_bird_units' => $earlyBirdUnits,
            'regular_units' => $regularUnits,
            'early_bird_unit_price' => $earlyBirdUnitPrice,
            'regular_unit_price' => $regularUnitPrice,
            'group_discount_percentage' => $groupDiscountPercent,
            'is_group_discount_applied' => $groupDiscountPercent > 0,
            'is_early_bird_applied' => $earlyBirdUnits > 0,
            'per_attendee_amounts' => $perAttendeeAmounts,
        ];
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
