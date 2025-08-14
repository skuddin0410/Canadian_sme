<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TicketPricingRule;
use App\Models\TicketType;
use Illuminate\Http\Request;

class TicketPricingController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketPricingRule::with('ticketType.event');
        
        if ($request->filled('ticket_type_id')) {
            $query->where('ticket_type_id', $request->ticket_type_id);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $pricingRules = $query->orderBy('created_at', 'desc')->paginate(15);
        $ticketTypes = TicketType::active()->with('event')->get();
        
        return view('tickets.pricing.index', compact('pricingRules', 'ticketTypes'));
    }

    public function create()
    {
        $ticketTypes = TicketType::active()->with('event')->get();
        return view('tickets.pricing.create', compact('ticketTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:early_bird,group,promo_code,late_bird,member_discount',
            'price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'min_quantity' => 'nullable|integer|min:1',
            'max_quantity' => 'nullable|integer|min:1',
            'usage_limit' => 'nullable|integer|min:1',
            'conditions' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        TicketPricingRule::create($request->all());

        return redirect()->route('admin.ticket-pricing.index')
                        ->with('success', 'Pricing rule created successfully.');
    }

    public function show(TicketPricingRule $ticketPricing)
    {
        $ticketPricing->load('ticketType.event');
        return view('tickets.pricing.show', compact('ticketPricing'));
    }

    public function edit(TicketPricingRule $ticketPricing)
    {
        $ticketTypes = TicketType::active()->with('event')->get();
        return view('tickets.pricing.edit', compact('ticketPricing', 'ticketTypes'));
    }

    public function update(Request $request, TicketPricingRule $ticketPricing)
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:early_bird,group,promo_code,late_bird,member_discount',
            'price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'min_quantity' => 'nullable|integer|min:1',
            'max_quantity' => 'nullable|integer|min:1',
            'usage_limit' => 'nullable|integer|min:1',
            'conditions' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $ticketPricing->update($request->all());

        return redirect()->route('admin.ticket-pricing.index')
                        ->with('success', 'Pricing rule updated successfully.');
    }

    public function destroy(TicketPricingRule $ticketPricing)
    {
        $ticketPricing->delete();
        
        return redirect()->route('admin.ticket-pricing.index')
                        ->with('success', 'Pricing rule deleted successfully.');
    }

    public function toggle(TicketPricingRule $ticketPricing)
    {
        $ticketPricing->update(['is_active' => !$ticketPricing->is_active]);
        
        $status = $ticketPricing->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
                        ->with('success', "Pricing rule {$status} successfully.");
    }
}