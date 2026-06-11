<?php

namespace App\Http\Controllers;

use App\Models\EventAndEntityLink;
use App\Models\Event;
use App\Models\PromoCodeRedemption;
use App\Models\TicketOrder;
use App\Models\TicketPurchase;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Refund;
use Stripe\Stripe;

class TicketPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketOrder::with(['coordinatorUser', 'ticketType', 'event', 'attendeePurchases.user']);

        if ($request->filled('event_id')) {
            $eventId = $request->event_id;
            if (!isSuperAdmin() && !in_array($eventId, getEventIds())) {
                $eventId = 0;
            }
            $query->where('event_id', $eventId);
        } elseif (!isSuperAdmin()) {
            $query->whereIn('event_id', getEventIds());
        }

        if ($request->filled('ticket_type_id')) {
            $query->where('ticket_type_id', $request->ticket_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('payment_reference', 'like', '%' . $search . '%')
                    ->orWhere('coordinator_name', 'like', '%' . $search . '%')
                    ->orWhere('coordinator_email', 'like', '%' . $search . '%')
                    ->orWhereHas('coordinatorUser', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('lastname', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('ticketType', function ($ticketQuery) use ($search) {
                        $ticketQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('event', function ($eventQuery) use ($search) {
                        $eventQuery->where('title', 'like', '%' . $search . '%');
                    });
            });
        }

        $ticketPurchases = $query->latest()->paginate(20)->withQueryString();

        $events = isSuperAdmin() 
            ? Event::orderBy('title')->get(['id', 'title'])
            : Event::orderBy('title')->whereIn('id', getEventIds())->get(['id', 'title']);
        $ticketTypes = isSuperAdmin()
            ? TicketType::orderBy('name')->get(['id', 'name'])
            : TicketType::orderBy('name')->whereIn('event_id', getEventIds())->get(['id', 'name']);
        $statuses = TicketOrder::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        return view('tickets.purchases.index', compact('ticketPurchases', 'events', 'ticketTypes', 'statuses'));
    }

    public function refund(Request $request, TicketOrder $ticketOrder)
    {
        if (!isSuperAdmin() && !in_array($ticketOrder->event_id, getEventIds())) {
            abort(403);
        }

        if ($ticketOrder->status !== 'completed') {
            return back()->with('error', 'Only completed transactions can be refunded.');
        }

        if (blank($ticketOrder->payment_reference)) {
            return back()->with('error', 'This transaction does not have a valid payment reference for refund.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $stripeRefund = Refund::create([
                'payment_intent' => $ticketOrder->payment_reference,
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'ticket_order_id' => (string) $ticketOrder->id,
                    'event_id' => (string) $ticketOrder->event_id,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', app()->environment('local')
                ? 'Refund failed: ' . $e->getMessage()
                : 'Refund could not be completed. Please try again or contact support.');
        }

        DB::transaction(function () use ($ticketOrder, $stripeRefund) {
            $ticketOrder->loadMissing('ticketType', 'attendeePurchases.user');

            $ticketOrder->update([
                'status' => 'refunded',
                'response' => array_merge($ticketOrder->response ?? [], [
                    'stripe_refund_id' => $stripeRefund->id,
                    'stripe_refund_status' => $stripeRefund->status,
                    'stripe_refund_amount' => ($stripeRefund->amount ?? 0) / 100,
                    'refunded_at' => now()->toDateTimeString(),
                ]),
            ]);

            TicketPurchase::where('ticket_order_id', $ticketOrder->id)->update([
                'status' => 'refunded',
            ]);

            $ticket = TicketType::lockForUpdate()->find($ticketOrder->ticket_type_id);
            if ($ticket) {
                $ticket->increment('available_quantity', (int) $ticketOrder->attendee_count);
            }

            PromoCodeRedemption::where('ticket_order_id', $ticketOrder->id)
                ->where('status', 'completed')
                ->update([
                    'status' => 'refunded',
                    'refunded_at' => now(),
                ]);

            foreach ($ticketOrder->attendeePurchases as $attendeePurchase) {
                if (!$attendeePurchase->user_id) {
                    continue;
                }

                $hasOtherValidAccess = TicketPurchase::query()
                    ->where('event_id', $ticketOrder->event_id)
                    ->where('user_id', $attendeePurchase->user_id)
                    ->where('id', '!=', $attendeePurchase->id)
                    ->where('status', 'completed')
                    ->exists();

                if (!$hasOtherValidAccess) {
                    EventAndEntityLink::where('event_id', $ticketOrder->event_id)
                        ->where('entity_type', 'users')
                        ->where('entity_id', $attendeePurchase->user_id)
                        ->delete();
                }
            }
        });

        return back()->with('success', 'Full refund completed successfully and attendee event access has been revoked where applicable.');
    }

    public function analytics(Request $request)
    {
        $baseQuery = TicketOrder::query()
            ->with(['event:id,title', 'ticketType:id,name', 'coordinatorUser:id,name,lastname,email']);

        if ($request->filled('event_id')) {
            $eventId = $request->event_id;
            if (!isSuperAdmin() && !in_array($eventId, getEventIds())) {
                $eventId = 0;
            }
            $baseQuery->where('ticket_orders.event_id', $eventId);
        } elseif (!isSuperAdmin()) {
            $baseQuery->whereIn('ticket_orders.event_id', getEventIds());
        }

        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $baseQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $baseQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $summaryQuery = clone $baseQuery;
        $completedRevenueQuery = clone $baseQuery;
        $statusBreakdownQuery = clone $baseQuery;
        $ticketBreakdownQuery = clone $baseQuery;
        $eventBreakdownQuery = clone $baseQuery;
        $recentPurchasesQuery = clone $baseQuery;

        $kpis = [
            'total_purchases' => $summaryQuery->count(),
            'completed_purchases' => (clone $baseQuery)->where('status', 'completed')->count(),
            'pending_purchases' => (clone $baseQuery)->where('status', 'pending_payment')->count(),
            'completed_revenue' => (float) $completedRevenueQuery->where('status', 'completed')->sum('total_amount'),
        ];

        $statusBreakdown = $statusBreakdownQuery
            ->select('status', DB::raw('COUNT(*) as purchases'), DB::raw('SUM(total_amount) as amount'))
            ->groupBy('status')
            ->orderByDesc('purchases')
            ->get();

        $ticketBreakdown = $ticketBreakdownQuery
            ->join('ticket_types', 'ticket_orders.ticket_type_id', '=', 'ticket_types.id')
            ->select('ticket_types.name', DB::raw('COUNT(ticket_orders.id) as purchases'), DB::raw('SUM(ticket_orders.total_amount) as amount'))
            ->groupBy('ticket_types.id', 'ticket_types.name')
            ->orderByDesc('purchases')
            ->get();

        $eventBreakdown = $eventBreakdownQuery
            ->join('events', 'ticket_orders.event_id', '=', 'events.id')
            ->select('events.title', DB::raw('COUNT(ticket_orders.id) as purchases'), DB::raw('SUM(ticket_orders.total_amount) as amount'))
            ->groupBy('events.id', 'events.title')
            ->orderByDesc('purchases')
            ->get();

        $recentPurchases = $recentPurchasesQuery
            ->latest()
            ->limit(10)
            ->get();

        $statusChartData = $statusBreakdown->map(function ($row) {
            return [
                'label' => ucwords(str_replace('_', ' ', $row->status)),
                'purchases' => (int) $row->purchases,
            ];
        })->values();

        $ticketTypeChartData = $ticketBreakdown->take(8)->map(function ($row) {
            return [
                'label' => $row->name,
                'purchases' => (int) $row->purchases,
            ];
        })->values();

        $eventChartData = $eventBreakdown->take(8)->map(function ($row) {
            return [
                'label' => $row->title,
                'purchases' => (int) $row->purchases,
            ];
        })->values();

        $events = isSuperAdmin()
            ? Event::orderBy('title')->get(['id', 'title'])
            : Event::orderBy('title')->whereIn('id', getEventIds())->get(['id', 'title']);
        $statuses = TicketOrder::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        return view('admin.analytics.ticket-purchases', compact(
            'kpis',
            'statusBreakdown',
            'ticketBreakdown',
            'eventBreakdown',
            'recentPurchases',
            'statusChartData',
            'ticketTypeChartData',
            'eventChartData',
            'events',
            'statuses'
        ));
    }
}
