<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Pricing;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::paginate(10);
        return view('subscription.index', compact('subscriptions'));
    }

    public function history()
    {
        $user_id = auth()->id();
        $subscriptions = Subscription::where('user_id', $user_id)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
            
        return view('subscription.history', compact('subscriptions'));
    }
    public function create()
    {
        $users = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Admin"]);
        })->where('id', '!=', 1)
            ->get();
        $events = Event::all();
        $pricings = Pricing::all();
        return view('subscription.create', compact('users', 'events', 'pricings'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'price_id' => 'required|exists:pricing,id',
            'user_id' => 'required|exists:users,id',
            'event_count' => 'required|integer|min:1',
            'attendee_count' => 'required|integer|min:1',
            // 'expired_at' => 'required|integer|min:1|max:24', // Original months validation
            'expired_at' => 'required|integer|min:1|max:3650', // New days validation
            'status' => 'required|in:active,inactive',
        ]);

        $expiredAt = null;

        if ($request->expired_at) {
            /* 
            // Original months logic
            $months = (int) $request->expired_at;
            $expiredAt = now()->addMonths($months);
            */

            // New days logic
            $days = (int) $request->expired_at;
            $expiredAt = now()->addDays($days);
        }

        // Auto-deactivate logically expired subscriptions for this user first
        Subscription::where('user_id', $request->user_id)
            ->where('status', 'active')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<=', now())
            ->update(['status' => 'inactive']);

        // Prevent duplicate truly active subscription (Status 'active' AND not expired)
        $exists = Subscription::where('user_id', $request->user_id)
            ->active()
            ->exists();

        // Only block if force_create is NOT set
        if ($exists && !$request->has('force_create')) {
            return back()->withErrors(['error' => 'An active subscription already exists for this user.'])->withInput();
        }

        Subscription::create([
            'user_id' => $request->user_id,
            'price_id' => $request->price_id,
            'attendee_count' => $request->attendee_count,
            'event_count' => $request->event_count,
            'expired_at' => $expiredAt,
            'status' => $request->status,
        ]);

        return redirect()->route('subscription.index')->with('success', 'Subscription created successfully.');
    }
    public function show($id)
    {
        $subscription = Subscription::with(['user', 'event', 'pricing'])->findOrFail($id);
        $logs = \App\Models\AuditLog::where('auditable_type', get_class($subscription))
            ->where('auditable_id', $subscription->id)
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('subscription.show', compact('subscription', 'logs'));
    }
    public function edit($id)
    {
        $subscription = Subscription::findOrFail($id);

        $users = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Admin"]);
        })->where('id', '!=', 1)
            ->get();

        $pricings = Pricing::all();

        // No longer needed but kept for reference
        /*
        $monthsRemaining = null;
        if ($subscription->expired_at) {
            $monthsRemaining = round(now()->diffInMonths($subscription->expired_at, false));
            // If expired already → set 0
            if ($monthsRemaining < 0) { $monthsRemaining = 0; }
        }
        */

        return view('subscription.edit', compact('subscription', 'users', 'pricings' /*, 'monthsRemaining' */));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'price_id' => 'required|exists:pricing,id',
            // 'user_id' => 'required|exists:users,id',
            'event_count' => 'required|integer|min:1',
            'attendee_count' => 'required|integer|min:1',
            // 'expired_at' => 'nullable|integer|min:1|max:24',
            'status' => 'required|in:active,inactive',
        ]);

        $subscription = Subscription::findOrFail($id);

        // Security check: cannot reactivate expired subscriptions
        if ($subscription->expired_at && $subscription->expired_at->isPast()) {
            if ($request->status == 'active') {
                return back()->withErrors(['error' => 'This subscription has expired and cannot be reactivated. Please create a new subscription instead.'])->withInput();
            }
        }

        $oldValues = $subscription->only(['price_id', 'attendee_count', 'event_count', 'status']);

        $subscription->update([
            'price_id' => $request->price_id,
            'attendee_count' => $request->attendee_count,
            'event_count' => $request->event_count,
            'status' => $request->status,
        ]);

        $newValues = $subscription->only(['price_id', 'attendee_count', 'event_count', 'status']);
        
        // Log changes if any
        $changes = array_diff_assoc($newValues, $oldValues);
        if (!empty($changes)) {
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'user_type' => get_class(auth()->user()),
                'event' => 'updated',
                'auditable_type' => get_class($subscription),
                'auditable_id' => $subscription->id,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return redirect()->route('subscription.index')
            ->with('success', 'Subscription updated successfully.');
    }
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return redirect()->route('subscription.index')
            ->with('success', 'Subscription deleted successfully.');
    }

    public function checkActiveSubscription(Request $request)
    {
        $userId = $request->user_id;
        $exists = Subscription::where('user_id', $userId)
            ->active()
            ->first();

        if ($exists) {
            return response()->json([
                'exists' => true,
                'plan_name' => $exists->pricing->name ?? 'Unknown Plan',
                'expired_at' => $exists->expired_at ? $exists->expired_at->format('d M Y') : 'No Expiry'
            ]);
        }

        return response()->json(['exists' => false]);
    }
}
