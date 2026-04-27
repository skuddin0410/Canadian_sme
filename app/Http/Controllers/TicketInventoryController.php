<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use App\Models\TicketInventoryLog;
use Illuminate\Http\Request;

class TicketInventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketType::with(['event', 'category']);
        
        if ($request->filled('event_id')) {
            $eventId = $request->event_id;
            if (!isSuperAdmin() && !in_array($eventId, getEventIds())) {
                $eventId = 0;
            }
            $query->where('event_id', $eventId);
        } elseif (!isSuperAdmin()) {
            $query->whereIn('event_id', getEventIds());
        }
        
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'low_stock':
                    $query->whereRaw('available_quantity <= (total_quantity * 0.1)');
                    break;
                case 'sold_out':
                    $query->where('available_quantity', 0);
                    break;
                case 'available':
                    $query->where('available_quantity', '>', 0);
                    break;
            }
        }
        
        $ticketTypes = $query->orderBy('available_quantity', 'asc')->paginate(15);
        
        // Get inventory summary
        $summaryQuery = TicketType::query();
        if (!isSuperAdmin()) {
            $summaryQuery->whereIn('event_id', getEventIds());
        }

        $totalTickets = (clone $summaryQuery)->sum('total_quantity');
        $availableTickets = (clone $summaryQuery)->sum('available_quantity');
        $soldTickets = $totalTickets - $availableTickets;
        $lowStockCount = (clone $summaryQuery)->whereRaw('available_quantity <= (total_quantity * 0.1)')
                                  ->where('available_quantity', '>', 0)
                                  ->count();
        $soldOutCount = (clone $summaryQuery)->where('available_quantity', 0)->count();
        
        return view('tickets.inventory.index', compact(
            'ticketTypes', 'totalTickets', 'availableTickets', 
            'soldTickets', 'lowStockCount', 'soldOutCount'
        ));
    }

    public function logs(Request $request)
    {
        $query = TicketInventoryLog::with(['ticketType.event', 'user']);

        if (!isSuperAdmin()) {
            $query->whereHas('ticketType', function($q) {
                $q->whereIn('event_id', getEventIds());
            });
        }
        
        if ($request->filled('ticket_type_id')) {
            $query->where('ticket_type_id', $request->ticket_type_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        $ticketTypes = isSuperAdmin() 
            ? TicketType::with('event')->get()
            : TicketType::with('event')->whereIn('event_id', getEventIds())->get();
        
        return view('tickets.inventory.logs', compact('logs', 'ticketTypes'));
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'updates.*.action' => 'required|in:set,increase,decrease',
            'updates.*.quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255'
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($request->updates as $update) {
            try {
                $ticketType = TicketType::find($update['ticket_type_id']);
                $quantity = $update['quantity'];
                $action = $update['action'];

                switch ($action) {
                    case 'set':
                        $difference = $quantity - $ticketType->available_quantity;
                        $ticketType->update([
                            'available_quantity' => $quantity,
                            'total_quantity' => $ticketType->total_quantity + $difference
                        ]);
                        $ticketType->logInventoryChange(
                            $difference >= 0 ? 'increase' : 'decrease',
                            abs($difference),
                            $request->reason ?? 'Bulk inventory update'
                        );
                        break;
                        
                    case 'increase':
                        $ticketType->increment('available_quantity', $quantity);
                        $ticketType->increment('total_quantity', $quantity);
                        $ticketType->logInventoryChange('increase', $quantity, $request->reason);
                        break;
                        
                    case 'decrease':
                        if ($ticketType->available_quantity >= $quantity) {
                            $ticketType->decrement('available_quantity', $quantity);
                            $ticketType->decrement('total_quantity', $quantity);
                            $ticketType->logInventoryChange('decrease', $quantity, $request->reason);
                        } else {
                            $errors[] = "Cannot decrease {$ticketType->name} by {$quantity} - insufficient stock";
                            continue 2;
                        }
                        break;
                }
                
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Error updating {$ticketType->name}: " . $e->getMessage();
            }
        }

        $message = "{$successCount} ticket types updated successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return redirect()->back()->with('success', $message);
    }
}