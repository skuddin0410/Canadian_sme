<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use App\Models\TicketCategory;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketType::with(['event', 'category']);
        
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        $ticketTypes = $query->orderBy('sort_order')->orderBy('name')->paginate(15);
        $events = Event::whereIn('status',['draft', 'published'])->get();
        $categories = TicketCategory::active()->ordered()->get();
        
        return view('tickets.types.index', compact('ticketTypes', 'events', 'categories'));
    }

    public function create()
    {
        $events = Event::whereIn('status',['draft', 'published'])->get();
        $categories = TicketCategory::active()->ordered()->get();
        
        return view('tickets.types.create', compact('events', 'categories'));
    }

    public function store(Request $request)
    {   
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'category_id' => 'nullable|exists:ticket_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'total_quantity' => 'required|integer|min:1',
            'min_quantity_per_order' => 'required|integer|min:1',
            'max_quantity_per_order' => 'nullable|integer|min:1',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after:sale_start_date',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
            'access_permissions' => 'nullable|array'
        ]);

        $slug = Str::slug($request->name);
        $eventId = $request->event_id;
        
        // Ensure unique slug per event
        $counter = 1;
        $originalSlug = $slug;
        while (TicketType::where('event_id', $eventId)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $ticketType = TicketType::create([
            'event_id' => $eventId,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'base_price' => $request->base_price,
            'total_quantity' => $request->total_quantity,
            'available_quantity' => $request->total_quantity,
            'min_quantity_per_order' => $request->min_quantity_per_order,
            'max_quantity_per_order' => $request->max_quantity_per_order,
            'sale_start_date' => $request->sale_start_date,
            'sale_end_date' => $request->sale_end_date,
            'requires_approval' => $request->boolean('requires_approval'),
            'is_active' => $request->boolean('is_active', true),
            'access_permissions' => $request->access_permissions ?? []
        ]);

        return redirect()->route('admin.ticket-types.index')
                        ->with('success', 'Ticket type created successfully.');
    }

    public function show(TicketType $ticketType)
    {
        $ticketType->load(['event', 'category', 'pricingRules', 'inventoryLogs.user']);
        return view('tickets.types.show', compact('ticketType'));
    }

    public function edit(TicketType $ticketType)
    {
        $events = Event::whereIn('status',['draft', 'published'])->get();
        $categories = TicketCategory::active()->ordered()->get();
        
        return view('tickets.types.edit', compact('ticketType', 'events', 'categories'));
    }

    public function update(Request $request, TicketType $ticketType)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'category_id' => 'nullable|exists:ticket_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'total_quantity' => 'required|integer|min:1',
            'min_quantity_per_order' => 'required|integer|min:1',
            'max_quantity_per_order' => 'nullable|integer|min:1',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after:sale_start_date',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
            'access_permissions' => 'nullable|array'
        ]);

        // Handle quantity changes
        $quantityDifference = $request->total_quantity - $ticketType->total_quantity;
        if ($quantityDifference != 0) {
            $ticketType->available_quantity += $quantityDifference;
            $ticketType->logInventoryChange(
                $quantityDifference > 0 ? 'increase' : 'decrease',
                abs($quantityDifference),
                'Admin updated total quantity'
            );
        }

        $ticketType->update([
            'event_id' => $request->event_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'base_price' => $request->base_price,
            'total_quantity' => $request->total_quantity,
            'min_quantity_per_order' => $request->min_quantity_per_order,
            'max_quantity_per_order' => $request->max_quantity_per_order,
            'sale_start_date' => $request->sale_start_date,
            'sale_end_date' => $request->sale_end_date,
            'requires_approval' => $request->boolean('requires_approval'),
            'is_active' => $request->boolean('is_active'),
            'access_permissions' => $request->access_permissions ?? []
        ]);

        return redirect()->route('admin.ticket-types.index')
                        ->with('success', 'Ticket type updated successfully.');
    }

    public function destroy(TicketType $ticketType)
    {
        if ($ticketType->eventTickets()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete ticket type that has associated tickets.');
        }

        $ticketType->delete();
        
        return redirect()->route('admin.ticket-types.index')
                        ->with('success', 'Ticket type deleted successfully.');
    }

    public function updateInventory(Request $request, TicketType $ticketType)
    {
        $request->validate([
            'action' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255'
        ]);

        $quantity = $request->quantity;
        $action = $request->action;

        if ($action === 'decrease' && $ticketType->available_quantity < $quantity) {
            return redirect()->back()
                           ->with('error', 'Cannot decrease quantity below available amount.');
        }

        if ($action === 'increase') {
            $ticketType->increment('available_quantity', $quantity);
            $ticketType->increment('total_quantity', $quantity);
        } else {
            $ticketType->decrement('available_quantity', $quantity);
            $ticketType->decrement('total_quantity', $quantity);
        }

        $ticketType->logInventoryChange($action, $quantity, $request->reason);

        return redirect()->back()
                        ->with('success', 'Inventory updated successfully.');
    }
}