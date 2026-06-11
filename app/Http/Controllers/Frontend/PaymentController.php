<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendingRegistration;
use App\Models\PromoCodeRedemption;
use App\Models\TicketOrder;
use App\Models\TicketPurchase;
use App\Mail\RegistrationCredentialsMail;
use App\Mail\TicketInvoiceMail;
use App\Models\TicketType;
use App\Models\User;
use App\Services\TicketInvoiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Event;
use App\Models\EventAndEntityLink;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;


class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $pendingRegistrationId = $request->pending_registration_id;

        $pendingRegistration = PendingRegistration::with('ticketType')->findOrFail($pendingRegistrationId);

        if ($pendingRegistration->status === 'completed') {
            $event = Event::findOrFail($pendingRegistration->event_id);

            return redirect()
                ->route('event.user.register', $event->slug)
                ->with('success', 'This registration has already been paid.');
        }

        $ticket = $pendingRegistration->ticketType;
        $pendingData = $pendingRegistration->request ?? [];
        $attendeeCount = max((int) ($pendingData['attendee_count'] ?? 1), 1);
        $pricingSummary = $pendingData['pricing_summary'] ?? $ticket->getRegistrationPricing($attendeeCount);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $lineItems = $this->buildCheckoutLineItems($ticket, $pricingSummary);

        // Create Stripe Checkout Session
        $session = CheckoutSession::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('payment.success', ['pending_registration_id' => $pendingRegistration->id]) . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('payment.cancel', ['pending_registration_id' => $pendingRegistration->id]),
        ]);

        $pendingRegistration->update([
            'status' => 'checkout_started',
            'response' => [
                'checkout_session_id' => $session->id,
                'checkout_url' => $session->url,
            ],
        ]);

        return redirect($session->url);
    }



    public function success(Request $request)
    {
        $pendingRegistration = PendingRegistration::with('ticketType')
            ->findOrFail($request->pending_registration_id);

        $event = Event::findOrFail($pendingRegistration->event_id);
        $pendingData = $pendingRegistration->request ?? [];
        $registrationMode = $pendingData['registration_mode'] ?? 'single';
        $coordinatorAttending = (bool) ($pendingData['coordinator_attending'] ?? false);
        $checkoutSessionId = $request->session_id;

        if (blank($checkoutSessionId)) {
            return redirect()
                ->route('event.user.register', $event->slug)
                ->with('error', 'Missing payment session. Please try the payment again.');
        }

        $storedCheckoutSessionId = data_get($pendingRegistration->response, 'checkout_session_id');
        if ($storedCheckoutSessionId && $storedCheckoutSessionId !== $checkoutSessionId) {
            return redirect()
                ->route('event.user.register', $event->slug)
                ->with('error', 'Payment session mismatch. Please try again.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $checkoutSession = CheckoutSession::retrieve($checkoutSessionId);

        if (($checkoutSession->payment_status ?? null) !== 'paid') {
            return redirect()
                ->route('event.user.register', $event->slug)
                ->with('error', 'Payment is not completed yet. Please try again after payment succeeds.');
        }

        if ($pendingRegistration->status === 'completed') {
            return redirect()
                ->route(($registrationMode === 'single' || $coordinatorAttending) ? 'event.user.login' : 'event.user.register', $event->slug)
                ->with('success', ($registrationMode === 'single' || $coordinatorAttending)
                    ? 'Registration successful.'
                    : 'Team registration successful.');
        }

        DB::beginTransaction();
        try {
            $ticket = TicketType::lockForUpdate()->findOrFail($pendingRegistration->ticket_type_id);
            $createdUsers = collect();
            $perAttendeeAmounts = collect($pendingData['pricing_summary']['per_attendee_amounts'] ?? []);
            $registeredAttendees = [];

            if ($registrationMode === 'single' || $coordinatorAttending) {
                $createdUsers->push($this->createOrResolveAttendee([
                    'first_name' => $pendingData['first_name'] ?? '',
                    'last_name' => $pendingData['last_name'] ?? '',
                    'email' => $pendingData['email'] ?? '',
                    'mobile' => $pendingData['mobile'] ?? null,
                    'designation' => $pendingData['designation'] ?? null,
                    'company' => $pendingData['company'] ?? null,
                    'bio' => $pendingData['bio'] ?? null,
                    'password' => Hash::make(Str::random(32)),
                ], $event->id));
                $registeredAttendees[] = [
                    'name' => trim(($pendingData['first_name'] ?? '') . ' ' . ($pendingData['last_name'] ?? '')),
                    'email' => $pendingData['email'] ?? '',
                ];
            }

            foreach (($pendingData['team_members'] ?? []) as $member) {
                if (blank($member['email'] ?? null)) {
                    continue;
                }

                $createdUsers->push($this->createOrResolveAttendee([
                    'first_name' => $member['first_name'] ?? '',
                    'last_name' => $member['last_name'] ?? '',
                    'email' => $member['email'] ?? '',
                    'mobile' => $member['mobile'] ?? null,
                    'designation' => $member['designation'] ?? null,
                    'company' => $member['company'] ?? ($pendingData['company'] ?? null),
                    'bio' => $member['bio'] ?? null,
                    'password' => Hash::make(Str::random(32)),
                ], $event->id));
                $registeredAttendees[] = [
                    'name' => trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')),
                    'email' => $member['email'] ?? '',
                ];
            }

            if ($createdUsers->isEmpty()) {
                throw new \RuntimeException('No attendee user was created from this registration.');
            }

            if ($ticket->available_quantity < $createdUsers->count()) {
                throw new \RuntimeException('Ticket inventory is no longer available for this registration.');
            }

            $ticket->decrement('available_quantity', $createdUsers->count());

            $paymentReference = $checkoutSession->payment_intent ?: $checkoutSession->id;
            $purchaseIds = [];
            $coordinatorName = trim(($pendingData['first_name'] ?? '') . ' ' . ($pendingData['last_name'] ?? ''));
            $coordinatorEmail = $pendingData['email'] ?? null;
            $coordinatorUser = $createdUsers->firstWhere('email', $coordinatorEmail);

            $ticketOrder = TicketOrder::create([
                'event_id' => $pendingRegistration->event_id,
                'ticket_type_id' => $pendingRegistration->ticket_type_id,
                'promo_code_id' => $pendingData['promo_code_id'] ?? null,
                'coordinator_user_id' => $coordinatorUser?->id,
                'coordinator_name' => $coordinatorName ?: null,
                'coordinator_email' => $coordinatorEmail,
                'attendee_count' => $createdUsers->count(),
                'total_amount' => $pendingRegistration->amount,
                'promo_discount_amount' => $pendingData['pricing_summary']['promo_discount'] ?? 0,
                'currency' => 'USD',
                'request' => $pendingRegistration->request,
                'response' => array_merge($pendingRegistration->response ?? [], [
                    'stripe_checkout_session_id' => $checkoutSession->id,
                    'stripe_payment_status' => $checkoutSession->payment_status,
                ]),
                'status' => 'completed',
                'payment_reference' => $paymentReference,
            ]);

            $ticketInvoice = app(TicketInvoiceService::class)->createDraftForOrder($ticketOrder);

            foreach ($createdUsers as $index => $createdUser) {
                $purchaseAmount = isset($perAttendeeAmounts[$index])
                    ? round((float) $perAttendeeAmounts[$index], 2)
                    : round((float) $pendingRegistration->amount / max($createdUsers->count(), 1), 2);

                $ticketPurchase = TicketPurchase::create([
                    'ticket_order_id' => $ticketOrder->id,
                    'user_id' => $createdUser->id,
                    'ticket_type_id' => $pendingRegistration->ticket_type_id,
                    'event_id' => $pendingRegistration->event_id,
                    'amount' => $purchaseAmount,
                    'request' => $pendingRegistration->request,
                    'response' => array_merge($pendingRegistration->response ?? [], [
                        'stripe_checkout_session_id' => $checkoutSession->id,
                        'stripe_payment_status' => $checkoutSession->payment_status,
                        'attendee_user_id' => $createdUser->id,
                    ]),
                    'status' => 'completed',
                    'payment_reference' => $paymentReference,
                ]);

                $purchaseIds[] = $ticketPurchase->id;
            }

            $pendingRegistration->update([
                'status' => 'completed',
                'payment_reference' => $paymentReference,
                'response' => array_merge($pendingRegistration->response ?? [], [
                    'stripe_checkout_session_id' => $checkoutSession->id,
                    'stripe_payment_status' => $checkoutSession->payment_status,
                    'ticket_invoice_id' => $ticketInvoice->id,
                    'ticket_order_id' => $ticketOrder->id,
                    'ticket_purchase_id' => $purchaseIds[0] ?? null,
                    'ticket_purchase_ids' => $purchaseIds,
                ]),
            ]);

            if (!empty($pendingData['promo_code_id'])) {
                PromoCodeRedemption::where('pending_registration_id', $pendingRegistration->id)
                    ->where('status', 'pending')
                    ->update([
                        'ticket_order_id' => $ticketOrder->id,
                        'user_id' => $coordinatorUser?->id,
                        'status' => 'completed',
                        'used_at' => now(),
                    ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return redirect()
                ->route('event.user.register', $event->slug)
                ->with('error', 'Payment succeeded, but attendee creation failed. Please contact support.');
        }

        Auth::logout();
        Session::flush();
        Session::regenerateToken();

        $createdUserIds = $createdUsers->pluck('id')->all();
        $totalRegistrations = $createdUsers->count();
        $coordinatorName = trim(($pendingData['first_name'] ?? '') . ' ' . ($pendingData['last_name'] ?? ''));
        $isTeamRegistration = $registrationMode === 'team';
        $mailRecipientEmail = $isTeamRegistration ? ($pendingData['email'] ?? null) : ($registeredAttendees[0]['email'] ?? null);
        $mailRecipientName = $isTeamRegistration ? $coordinatorName : ($registeredAttendees[0]['name'] ?? '');
        $loginUrl = route('event.user.login', ['event' => $event->slug]);
        $ticketOrderId = $ticketOrder->id;

        $response = redirect()
            ->route(($registrationMode === 'single' || $coordinatorAttending) ? 'event.user.login' : 'event.user.register', $event->slug)
            ->with('success', ($registrationMode === 'single' || $coordinatorAttending)
                ? 'Registration successful.'
                : 'Team registration successful.');

        dispatch(function () use ($createdUserIds, $event, $totalRegistrations, $coordinatorName, $mailRecipientEmail, $mailRecipientName, $registeredAttendees, $isTeamRegistration, $loginUrl, $ticketOrderId) {
            $users = User::whereIn('id', $createdUserIds)->get();

            foreach ($users as $createdUser) {
                try {
                    notification($createdUser->id);
                    if (qrCode($createdUser->id)) {
                        sendNotification("Welcome Email", $createdUser);
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            try {
                $ticketOrder = TicketOrder::with(['event', 'ticketType', 'attendeePurchases.user', 'invoice'])
                    ->find($ticketOrderId);

                if ($ticketOrder && $ticketOrder->invoice && $ticketOrder->coordinator_email) {
                    $invoiceService = app(TicketInvoiceService::class);
                    $invoice = $invoiceService->generateAndStore($ticketOrder->invoice);

                    Mail::to($ticketOrder->coordinator_email)->send(new TicketInvoiceMail($invoice));

                    $invoice->forceFill([
                        'sent_at' => now(),
                    ])->save();
                }
            } catch (\Throwable $e) {
                report($e);
            }

            if ($mailRecipientEmail && !empty($registeredAttendees)) {
                try {
                    Mail::to($mailRecipientEmail)->send(
                        new RegistrationCredentialsMail(
                            $mailRecipientName ?: 'Participant',
                            $event,
                            $loginUrl,
                            $registeredAttendees,
                            $isTeamRegistration
                        )
                    );
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $superAdminId = 1;
            $eventAdminId = $event->created_by;
            $notificationData = [
                'title' => 'New Self-Registration (Paid)',
                'body' => $totalRegistrations > 1
                    ? $totalRegistrations . ' attendees have been registered for the event "' . $event->title . '" by coordinator "' . ($coordinatorName ?: 'Unknown') . '"'
                    : 'A new attendee has self-registered for the event "' . $event->title . '"',
                'related_type' => 'event',
                'related_id' => $event->id,
                'is_read' => 0,
            ];
            \App\Models\GeneralNotification::create(array_merge($notificationData, ['user_id' => $superAdminId]));
            if ($eventAdminId && $eventAdminId != $superAdminId) {
                \App\Models\GeneralNotification::create(array_merge($notificationData, ['user_id' => $eventAdminId]));
            }
        })->afterResponse();

        return $response;
    }

    public function cancel(Request $request)
    {
        $pendingRegistration = PendingRegistration::findOrFail($request->pending_registration_id);
        $event = Event::findOrFail($pendingRegistration->event_id);

        if ($pendingRegistration->status !== 'completed') {
            $pendingRegistration->update(['status' => 'cancelled']);
        }

        return redirect()->route('event.user.register', $event->slug)
            ->with('error', 'Payment cancelled. Please try again.');
    }

    protected function createOrResolveAttendee(array $data, int $eventId): User
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            $user = User::create([
                'name'        => $data['first_name'],
                'lastname'    => $data['last_name'],
                'email'       => $data['email'],
                'mobile'      => $data['mobile'] ?? null,
                'designation' => $data['designation'] ?? null,
                'company'     => $data['company'] ?? null,
                'bio'         => $data['bio'] ?? null,
                'password'    => $data['password'],
            ]);
        }

        if (!$user->hasRole('Attendee')) {
            $user->assignRole('Attendee');
        }

        EventAndEntityLink::firstOrCreate([
            'event_id'    => $eventId,
            'entity_type' => 'users',
            'entity_id'   => $user->id,
        ]);

        return $user;
    }

    protected function buildCheckoutLineItems(TicketType $ticket, array $pricingSummary): array
    {
        $lineItems = [];
        $description = blank($ticket->description) ? null : $ticket->description;

        if ((float) ($pricingSummary['promo_discount'] ?? 0) > 0) {
            $productData = [
                'name' => $ticket->name . ' - Promo Applied',
            ];
            if ($description) {
                $productData['description'] = $description;
            }

            return [[
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) round((float) ($pricingSummary['total'] ?? 0) * 100),
                    'product_data' => $productData,
                ],
                'quantity' => 1,
            ]];
        }

        if (($pricingSummary['early_bird_units'] ?? 0) > 0) {
            $productData = [
                'name' => $ticket->name . ' - Early Bird',
            ];
            if ($description) {
                $productData['description'] = $description;
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) round(($pricingSummary['early_bird_unit_price'] ?? 0) * 100),
                    'product_data' => $productData,
                ],
                'quantity' => (int) $pricingSummary['early_bird_units'],
            ];
        }

        if (($pricingSummary['regular_units'] ?? 0) > 0) {
            $productData = [
                'name' => $ticket->name,
            ];
            if ($description) {
                $productData['description'] = $description;
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) round(($pricingSummary['regular_unit_price'] ?? $ticket->base_price) * 100),
                    'product_data' => $productData,
                ],
                'quantity' => (int) $pricingSummary['regular_units'],
            ];
        }

        if (empty($lineItems)) {
            $productData = [
                'name' => $ticket->name,
            ];
            if ($description) {
                $productData['description'] = $description;
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) round((float) $ticket->base_price * 100),
                    'product_data' => $productData,
                ],
                'quantity' => 1,
            ];
        }

        return $lineItems;
    }
}
