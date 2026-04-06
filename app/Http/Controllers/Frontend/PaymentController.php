<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketPurchase;
use App\Models\User;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Event;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $ticketPurchaseId = $request->ticket_purchase_id;

        // Fetch the TicketPurchase record
        $ticketPurchase = TicketPurchase::findOrFail($ticketPurchaseId);

        $ticket = $ticketPurchase->ticketType;

        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Create Stripe Checkout Session
        $session = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd', // change if needed
                    'unit_amount' => $ticketPurchase->amount * 100, // in cents
                    'product_data' => [
                        'name' => $ticket->name,
                        'description' => $ticket->description ?? '',
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['ticket_purchase_id' => $ticketPurchase->id]),
            'cancel_url'  => route('payment.cancel', ['ticket_purchase_id' => $ticketPurchase->id]),
        ]);

        return redirect($session->url);
    }



    public function success(Request $request)
    {
        $ticketPurchase = TicketPurchase::with('ticketType')
            ->findOrFail($request->ticket_purchase_id);

        // 1. Mark payment completed
        $ticketPurchase->update([
            'status' => 'completed',
            'payment_reference' => now()->timestamp,
        ]);

        $user  = $ticketPurchase->user;
        $event = Event::findOrFail($ticketPurchase->event_id);

        // 2. Send notifications / QR
        notification($user->id);
        if (qrCode($user->id)) {
            sendNotification("Welcome Email", $user);
        }

        // 3. Logout and destroy session completely
        Auth::logout();
        Session::flush();
        Session::regenerateToken();

        // 4. Redirect to EVENT LOGIN page (very important)
        return redirect()
            ->route('event.user.login', $event->id)
            ->with('success', 'Payment successful! Please login to continue.');
    }

    public function cancel(Request $request)
    {
        $ticketPurchase = TicketPurchase::findOrFail($request->ticket_purchase_id);

        $ticketPurchase->update(['status' => 'cancelled']);

        return redirect()->route('event.user.register', $ticketPurchase->event_id)
            ->with('error', 'Payment cancelled. Please try again.');
    }
}
