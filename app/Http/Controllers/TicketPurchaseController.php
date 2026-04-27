<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketPurchase;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketPurchase::with(['user', 'ticketType', 'event']);

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
                    ->orWhereHas('user', function ($userQuery) use ($search) {
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
        $statuses = TicketPurchase::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        return view('tickets.purchases.index', compact('ticketPurchases', 'events', 'ticketTypes', 'statuses'));
    }

    public function analytics(Request $request)
    {
        $baseQuery = TicketPurchase::query()
            ->with(['event:id,title', 'ticketType:id,name', 'user:id,name,lastname,email']);

        if ($request->filled('event_id')) {
            $eventId = $request->event_id;
            if (!isSuperAdmin() && !in_array($eventId, getEventIds())) {
                $eventId = 0;
            }
            $baseQuery->where('ticket_purchases.event_id', $eventId);
        } elseif (!isSuperAdmin()) {
            $baseQuery->whereIn('ticket_purchases.event_id', getEventIds());
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
            'completed_revenue' => (float) $completedRevenueQuery->where('status', 'completed')->sum('amount'),
        ];

        $statusBreakdown = $statusBreakdownQuery
            ->select('status', DB::raw('COUNT(*) as purchases'), DB::raw('SUM(amount) as amount'))
            ->groupBy('status')
            ->orderByDesc('purchases')
            ->get();

        $ticketBreakdown = $ticketBreakdownQuery
            ->join('ticket_types', 'ticket_purchases.ticket_type_id', '=', 'ticket_types.id')
            ->select('ticket_types.name', DB::raw('COUNT(ticket_purchases.id) as purchases'), DB::raw('SUM(ticket_purchases.amount) as amount'))
            ->groupBy('ticket_types.id', 'ticket_types.name')
            ->orderByDesc('purchases')
            ->get();

        $eventBreakdown = $eventBreakdownQuery
            ->join('events', 'ticket_purchases.event_id', '=', 'events.id')
            ->select('events.title', DB::raw('COUNT(ticket_purchases.id) as purchases'), DB::raw('SUM(ticket_purchases.amount) as amount'))
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
        $statuses = TicketPurchase::query()
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
