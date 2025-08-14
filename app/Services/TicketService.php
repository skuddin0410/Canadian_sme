<?php

namespace App\Services;

use App\Models\TicketType;
use App\Models\EventTicket;
use App\Models\EventTicketBooking;
use App\Models\TicketPricingRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TicketService
{
    public function calculateTicketPrice(TicketType $ticketType, int $quantity = 1, ?string $promoCode = null): array
    {
        $basePrice = $ticketType->base_price;
        $applicableRules = $this->getApplicablePricingRules($ticketType, $quantity, $promoCode);
        
        $bestRule = $applicableRules->sortBy('price')->first();
        $finalPrice = $bestRule ? $bestRule->price : $basePrice;
        
        $discount = $basePrice - $finalPrice;
        $total = $finalPrice * $quantity;
        
        // Apply taxes and fees
        $taxRate = config('tickets.defaults.tax_rate', 0);
        $bookingFee = config('tickets.defaults.booking_fee', 0);
        
        $taxAmount = $total * $taxRate;
        $grandTotal = $total + $taxAmount + $bookingFee;
        
        return [
            'base_price' => $basePrice,
            'final_price' => $finalPrice,
            'discount' => $discount,
            'subtotal' => $total,
            'tax_amount' => $taxAmount,
            'booking_fee' => $bookingFee,
            'grand_total' => $grandTotal,
            'applied_rule' => $bestRule,
            'savings' => $discount * $quantity,
        ];
    }
    
    public function reserveTickets(TicketType $ticketType, int $quantity): bool
    {
        return DB::transaction(function () use ($ticketType, $quantity) {
            // Lock the row to prevent race conditions
            $ticketType = TicketType::lockForUpdate()->find($ticketType->id);
            
            if ($ticketType->available_quantity >= $quantity) {
                $ticketType->decrement('available_quantity', $quantity);
                
                // Log the reservation
                $ticketType->inventoryLogs()->create([
                    'action' => 'reserve',
                    'quantity' => $quantity,
                    'previous_quantity' => $ticketType->available_quantity + $quantity,
                    'new_quantity' => $ticketType->available_quantity,
                    'reason' => 'Ticket reservation',
                    'user_id' => auth()->id(),
                ]);
                
                return true;
            }
            
            return false;
        });
    }
    
    public function releaseReservation(TicketType $ticketType, int $quantity): void
    {
        DB::transaction(function () use ($ticketType, $quantity) {
            $ticketType = TicketType::lockForUpdate()->find($ticketType->id);
            $ticketType->increment('available_quantity', $quantity);
            
            // Log the release
            $ticketType->inventoryLogs()->create([
                'action' => 'release',
                'quantity' => $quantity,
                'previous_quantity' => $ticketType->available_quantity - $quantity,
                'new_quantity' => $ticketType->available_quantity,
                'reason' => 'Reservation released',
                'user_id' => auth()->id(),
            ]);
        });
    }
    
    public function confirmBooking(EventTicketBooking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
            
            // Apply usage count to pricing rules if any
            if ($booking->discount_type && $booking->promo_code) {
                $this->incrementPromoCodeUsage($booking->promo_code);
            }
        });
    }
    
    public function cancelBooking(EventTicketBooking $booking, string $reason = null): void
    {
        DB::transaction(function () use ($booking, $reason) {
            // Release the reserved tickets
            $ticket = $booking->ticket;
            if ($ticket->ticketType) {
                $this->releaseReservation($ticket->ticketType, $booking->quantity);
            }
            
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);
        });
    }
    
    public function getInventorySummary(): array
    {
        $summary = TicketType::selectRaw('
            SUM(total_quantity) as total_tickets,
            SUM(available_quantity) as available_tickets,
            COUNT(CASE WHEN available_quantity = 0 THEN 1 END) as sold_out_count,
            COUNT(CASE WHEN available_quantity <= (total_quantity * 0.1) AND available_quantity > 0 THEN 1 END) as low_stock_count
        ')->first();
        
        $summary->sold_tickets = $summary->total_tickets - $summary->available_tickets;
        $summary->sell_through_rate = $summary->total_tickets > 0 
            ? ($summary->sold_tickets / $summary->total_tickets) * 100 
            : 0;
            
        return $summary->toArray();
    }
    
    public function getLowStockAlerts(): \Illuminate\Database\Eloquent\Collection
    {
        return TicketType::with(['event', 'category'])
            ->whereRaw('available_quantity <= (total_quantity * ?)', [config('tickets.inventory.low_stock_threshold')])
            ->where('available_quantity', '>', 0)
            ->where('is_active', true)
            ->orderBy('available_quantity', 'asc')
            ->get();
    }
    
    public function bulkUpdateInventory(array $updates, string $reason = null): array
    {
        $results = ['success' => 0, 'errors' => []];
        
        DB::transaction(function () use ($updates, $reason, &$results) {
            foreach ($updates as $update) {
                try {
                    $ticketType = TicketType::lockForUpdate()->find($update['ticket_type_id']);
                    $quantity = $update['quantity'];
                    $action = $update['action'];
                    
                    $previousQuantity = $ticketType->available_quantity;
                    
                    switch ($action) {
                        case 'set':
                            $difference = $quantity - $ticketType->available_quantity;
                            $ticketType->update([
                                'available_quantity' => $quantity,
                                'total_quantity' => $ticketType->total_quantity + $difference
                            ]);
                            break;
                            
                        case 'increase':
                            $ticketType->increment('available_quantity', $quantity);
                            $ticketType->increment('total_quantity', $quantity);
                            break;
                            
                        case 'decrease':
                            if ($ticketType->available_quantity >= $quantity) {
                                $ticketType->decrement('available_quantity', $quantity);
                                $ticketType->decrement('total_quantity', $quantity);
                            } else {
                                throw new \Exception("Insufficient stock for {$ticketType->name}");
                            }
                            break;
                    }
                    
                    // Log the change
                    $ticketType->inventoryLogs()->create([
                        'action' => $action === 'set' ? ($quantity > $previousQuantity ? 'increase' : 'decrease') : $action,
                        'quantity' => $action === 'set' ? abs($quantity - $previousQuantity) : $quantity,
                        'previous_quantity' => $previousQuantity,
                        'new_quantity' => $ticketType->available_quantity,
                        'reason' => $reason ?: 'Bulk inventory update',
                        'user_id' => auth()->id(),
                    ]);
                    
                    $results['success']++;
                    
                } catch (\Exception $e) {
                    $results['errors'][] = $e->getMessage();
                }
            }
        });
        
        return $results;
    }
    
    protected function getApplicablePricingRules(TicketType $ticketType, int $quantity, ?string $promoCode): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "pricing_rules_{$ticketType->id}_{$quantity}_" . ($promoCode ?: 'none');
        
        if (config('tickets.performance.cache_pricing_rules')) {
            return Cache::remember($cacheKey, config('tickets.performance.cache_duration'), function () use ($ticketType, $quantity, $promoCode) {
                return $this->fetchApplicablePricingRules($ticketType, $quantity, $promoCode);
            });
        }
        
        return $this->fetchApplicablePricingRules($ticketType, $quantity, $promoCode);
    }
    
    protected function fetchApplicablePricingRules(TicketType $ticketType, int $quantity, ?string $promoCode): \Illuminate\Database\Eloquent\Collection
    {
        return $ticketType->pricingRules()
            ->where('is_active', true)
            ->where(function ($query) use ($quantity, $promoCode) {
                // Group discount rules
                $query->where(function ($q) use ($quantity) {
                    $q->where('type', 'group')
                      ->where('min_quantity', '<=', $quantity)
                      ->where(function ($subQ) use ($quantity) {
                          $subQ->whereNull('max_quantity')
                               ->orWhere('max_quantity', '>=', $quantity);
                      });
                })
                // Time-based rules
                ->orWhere(function ($q) {
                    $q->whereIn('type', ['early_bird', 'late_bird'])
                      ->where(function ($subQ) {
                          $subQ->whereNull('start_date')
                               ->orWhere('start_date', '<=', now());
                      })
                      ->where(function ($subQ) {
                          $subQ->whereNull('end_date')
                               ->orWhere('end_date', '>=', now());
                      });
                })
                // Promo code rules
                ->when($promoCode, function ($q) use ($promoCode) {
                    $q->orWhere(function ($subQ) use ($promoCode) {
                        $subQ->where('type', 'promo_code')
                             ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(conditions, '$.code')) = ?", [$promoCode])
                             ->where(function ($usageQ) {
                                 $usageQ->whereNull('usage_limit')
                                        ->orWhereRaw('usage_count < usage_limit');
                             });
                    });
                });
            })
            ->get();
    }
    
    protected function incrementPromoCodeUsage(string $promoCode): void
    {
        TicketPricingRule::where('type', 'promo_code')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(conditions, '$.code')) = ?", [$promoCode])
            ->increment('usage_count');
    }
}

// app/Console/Commands/ReleaseExpiredReservations.php - Command to release expired reservations
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EventTicketBooking;
use App\Services\TicketService;
use Carbon\Carbon;

class ReleaseExpiredReservations extends Command
{
    protected $signature = 'tickets:release-expired';
    protected $description = 'Release expired ticket reservations';
    
    protected $ticketService;
    
    public function __construct(TicketService $ticketService)
    {
        parent::__construct();
        $this->ticketService = $ticketService;
    }
    
    public function handle()
    {
        $timeout = config('tickets.defaults.reservation_timeout', 15);
        $expiredTime = Carbon::now()->subMinutes($timeout);
        
        $expiredBookings = EventTicketBooking::where('status', 'pending')
            ->where('created_at', '<=', $expiredTime)
            ->with('ticket.ticketType')
            ->get();
            
        $count = 0;
        
        foreach ($expiredBookings as $booking) {
            try {
                $this->ticketService->cancelBooking($booking, 'Reservation expired');
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to release booking {$booking->id}: " . $e->getMessage());
            }
        }
        
        $this->info("Released {$count} expired reservations.");
        
        return 0;
    }
}

// Add to app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Release expired ticket reservations every 5 minutes
    $schedule->command('tickets:release-expired')
             ->everyFiveMinutes()
             ->withoutOverlapping();
}

// app/Http/Requests/StoreTicketTypeRequest.php - Form validation
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketTypeRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('create', TicketType::class);
    }

    public function rules()
    {
        return [
            'event_id' => 'required|exists:events,id',
            'category_id' => 'nullable|exists:ticket_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'base_price' => 'required|numeric|min:0|max:999999.99',
            'total_quantity' => 'required|integer|min:1|max:1000000',
            'min_quantity_per_order' => 'required|integer|min:1|max:100',
            'max_quantity_per_order' => 'nullable|integer|min:1|max:1000',
            'sale_start_date' => 'nullable|date|after:now',
            'sale_end_date' => 'nullable|date|after:sale_start_date',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
            'access_permissions' => 'nullable|array',
            'access_permissions.*' => 'string',
        ];
    }

    public function messages()
    {
        return [
            'event_id.required' => 'Please select an event.',
            'event_id.exists' => 'The selected event is invalid.',
            'name.required' => 'Ticket type name is required.',
            'base_price.required' => 'Base price is required.',
            'base_price.min' => 'Price cannot be negative.',
            'total_quantity.required' => 'Total quantity is required.',
            'total_quantity.min' => 'Minimum quantity is 1.',
            'sale_end_date.after' => 'Sale end date must be after start date.',
        ];
    }
}

// app/Http/Requests/UpdateTicketInventoryRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketInventoryRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('update', $this->route('ticketType'));
    }

    public function rules()
    {
        return [
            'action' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1|max:10000',
            'reason' => 'nullable|string|max:255',
        ];
    }

    protected function prepareForValidation()
    {
        // Additional validation for decrease action
        if ($this->input('action') === 'decrease') {
            $ticketType = $this->route('ticketType');
            $maxDecrease = $ticketType->available_quantity;
            
            $this->merge([
                'max_decrease' => $maxDecrease
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('action') === 'decrease') {
                $ticketType = $this->route('ticketType');
                if ($this->input('quantity') > $ticketType->available_quantity) {
                    $validator->errors()->add('quantity', 
                        'Cannot decrease by more than available quantity (' . $ticketType->available_quantity . ').');
                }
            }
        });
    }
}