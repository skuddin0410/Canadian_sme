<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketPurchase;
use App\Models\User;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

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
        $ticketPurchase = TicketPurchase::findOrFail($request->ticket_purchase_id);

        $ticketPurchase->update([
            'status' => 'completed',
            'payment_reference' => now()->timestamp, // you can store Stripe session ID too
        ]);

        $user = $ticketPurchase->user;

        // Send notifications / QR code
        notification($user->id);
        if (qrCode($user->id)) {
            sendNotification("Welcome Email", $user);
        }

        return redirect()->route('registration')->with('success', 'Payment successful! Your registration is confirmed.');
    }

    public function cancel(Request $request)
    {
        $ticketPurchase = TicketPurchase::findOrFail($request->ticket_purchase_id);

        // Optional: delete pending purchase if needed
        $ticketPurchase->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('form.show', ['id' => $ticketPurchase->form_id])
                         ->with('error', 'Payment cancelled. Please try again.');
    }
}
