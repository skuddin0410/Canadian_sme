<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GeneralNotification;
use App\Models\PromoCode;
use App\Models\PromoCodeRedemption;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PromoCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = PromoCode::with(['event', 'ticketType', 'creator'])
            ->withCount(['completedRedemptions as completed_redemptions_count'])
            ->withSum('completedRedemptions as completed_discount_amount', 'discount_amount');

        if ($request->filled('event_id')) {
            $eventId = $request->event_id;
            if (!isSuperAdmin() && !in_array((int) $eventId, getEventIds(), true)) {
                $eventId = 0;
            }
            $query->where('event_id', $eventId);
        } elseif (!isSuperAdmin()) {
            $query->whereIn('event_id', getEventIds());
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('code', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }

        $promoCodes = $query->latest()->paginate(20)->withQueryString();
        $events = $this->availableEvents();

        return view('tickets.promo-codes.index', compact('promoCodes', 'events'));
    }

    public function create()
    {
        $events = $this->availableEvents();
        $ticketTypes = $this->availableTicketTypes();

        return view('tickets.promo-codes.create', compact('events', 'ticketTypes'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePromoCode($request);
        $data['code'] = Str::upper($data['code']);
        $data['created_by'] = auth()->id();

        $promoCode = PromoCode::create($data);
        $creator = auth()->user();

        GeneralNotification::create([
            'user_id' => 1,
            'title' => 'Promo Code Created',
            'body' => 'Promo code "' . $promoCode->code . '" has been created by "' . ($creator?->full_name ?? $creator?->name ?? 'System') . '".',
            'related_type' => 'promo_code',
            'related_id' => $promoCode->id,
            'is_read' => 0,
        ]);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Promo code created successfully.');
    }

    public function edit(PromoCode $promoCode)
    {
        $this->authorizePromoCode($promoCode);

        $events = $this->availableEvents();
        $ticketTypes = $this->availableTicketTypes($promoCode->event_id);

        return view('tickets.promo-codes.edit', compact('promoCode', 'events', 'ticketTypes'));
    }

    public function update(Request $request, PromoCode $promoCode)
    {
        $this->authorizePromoCode($promoCode);

        $data = $this->validatePromoCode($request, $promoCode);
        $data['code'] = Str::upper($data['code']);

        $promoCode->update($data);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Promo code updated successfully.');
    }

    public function destroy(PromoCode $promoCode)
    {
        $this->authorizePromoCode($promoCode);

        if ($promoCode->redemptions()->whereIn('status', ['pending', 'completed'])->exists()) {
            return back()->with('error', 'This promo code already has redemptions and cannot be deleted.');
        }

        $promoCode->delete();

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Promo code deleted successfully.');
    }

    public function bulkCreate()
    {
        $events = $this->availableEvents();
        $ticketTypes = $this->availableTicketTypes();

        return view('tickets.promo-codes.bulk', compact('events', 'ticketTypes'));
    }

    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'ticket_type_id' => 'nullable|exists:ticket_types,id',
            'prefix' => 'nullable|string|max:20',
            'quantity' => 'required|integer|min:1|max:500',
            'discount_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit_total' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'min_attendee_count' => 'nullable|integer|min:1',
            'max_attendee_count' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $created = 0;
        $prefix = Str::upper(trim((string) ($data['prefix'] ?? 'PROMO')));

        for ($i = 0; $i < (int) $data['quantity']; $i++) {
            PromoCode::create([
                'event_id' => $data['event_id'],
                'ticket_type_id' => $data['ticket_type_id'] ?? null,
                'code' => $this->generateUniqueCode($prefix),
                'discount_type' => $data['discount_type'],
                'discount_value' => $data['discount_value'],
                'is_active' => $request->boolean('is_active', true),
                'starts_at' => $data['starts_at'] ?? null,
                'ends_at' => $data['ends_at'] ?? null,
                'usage_limit_total' => $data['usage_limit_total'] ?? null,
                'usage_limit_per_user' => $data['usage_limit_per_user'] ?? null,
                'min_attendee_count' => $data['min_attendee_count'] ?? null,
                'max_attendee_count' => $data['max_attendee_count'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
            $created++;
        }

        return redirect()->route('admin.promo-codes.index')
            ->with('success', $created . ' promo code(s) generated successfully.');
    }

    public function redemptions(Request $request)
    {
        $query = PromoCodeRedemption::with(['promoCode', 'event', 'ticketType', 'ticketOrder', 'user']);

        if ($request->filled('event_id')) {
            $eventId = $request->event_id;
            if (!isSuperAdmin() && !in_array((int) $eventId, getEventIds(), true)) {
                $eventId = 0;
            }
            $query->where('event_id', $eventId);
        } elseif (!isSuperAdmin()) {
            $query->whereIn('event_id', getEventIds());
        }

        if ($request->filled('promo_code_id')) {
            $query->where('promo_code_id', $request->promo_code_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $redemptions = $query->latest()->paginate(25)->withQueryString();
        $events = $this->availableEvents();
        $promoCodes = isSuperAdmin()
            ? PromoCode::orderBy('code')->get(['id', 'code'])
            : PromoCode::whereIn('event_id', getEventIds())->orderBy('code')->get(['id', 'code']);

        return view('tickets.promo-codes.redemptions', compact('redemptions', 'events', 'promoCodes'));
    }

    protected function validatePromoCode(Request $request, ?PromoCode $promoCode = null): array
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'ticket_type_id' => 'nullable|exists:ticket_types,id',
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('promo_codes', 'code')->ignore($promoCode?->id),
            ],
            'discount_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit_total' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'min_attendee_count' => 'nullable|integer|min:1',
            'max_attendee_count' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }

    protected function availableEvents()
    {
        return isSuperAdmin()
            ? Event::orderBy('title')->get(['id', 'title'])
            : Event::whereIn('id', getEventIds())->orderBy('title')->get(['id', 'title']);
    }

    protected function availableTicketTypes(?int $eventId = null)
    {
        $query = TicketType::orderBy('name');

        if ($eventId) {
            $query->where('event_id', $eventId);
        } elseif (!isSuperAdmin()) {
            $query->whereIn('event_id', getEventIds());
        }

        return $query->get(['id', 'name', 'event_id']);
    }

    protected function authorizePromoCode(PromoCode $promoCode): void
    {
        if (!isSuperAdmin() && !in_array((int) $promoCode->event_id, getEventIds(), true)) {
            abort(403, 'Unauthorized action.');
        }
    }

    protected function generateUniqueCode(string $prefix): string
    {
        do {
            $code = $prefix . '-' . Str::upper(Str::random(8));
        } while (PromoCode::where('code', $code)->exists());

        return $code;
    }
}
