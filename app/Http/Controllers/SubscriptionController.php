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
        $subscriptions = Subscription::all();
        $subscriptions = Subscription::paginate(10);
        return view('subscription.index', compact('subscriptions'));
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
            'expired_at' => 'required|integer|min:1|max:24',
            'status' => 'required|in:active,inactive',
        ]);

        $expiredAt = null;

        if ($request->expired_at) {
            $months = (int) $request->expired_at;
            $expiredAt = now()->addMonths($months);
        }

        // Prevent duplicate active subscription
        $exists = Subscription::where('user_id', $request->user_id)
            ->where('price_id', $request->price_id)
            ->where('status', 'active')
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'Active subscription already exists'])->withInput();
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

        return view('subscription.show', compact('subscription'));
    }
    public function edit($id)
    {
        $subscription = Subscription::findOrFail($id);

        $users = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Admin"]);
        })->where('id', '!=', 1)
        ->get();

        $pricings = Pricing::all();

        return view('subscription.edit', compact('subscription', 'users', 'pricings'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'price_id' => 'required|exists:pricing,id',
            'user_id' => 'required|exists:users,id',
            'event_count' => 'required|integer|min:1',
            'attendee_count' => 'required|integer|min:1',
            'expired_at' => 'nullable|integer|min:1|max:24',
            'status' => 'required|in:active,inactive',
        ]);

        $subscription = Subscription::findOrFail($id);

        // Convert months → datetime
        $expiredAt = null;
        if ($request->expired_at) {
            $expiredAt = now()->addMonths((int) $request->expired_at);
        }

        $subscription->update([
            'user_id' => $request->user_id,
            'price_id' => $request->price_id,
            'attendee_count' => $request->attendee_count,
            'event_count' => $request->event_count,
            'expired_at' => $expiredAt,
            'status' => $request->status,
        ]);

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
}
