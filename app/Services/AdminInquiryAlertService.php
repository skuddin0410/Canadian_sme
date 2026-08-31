<?php

namespace App\Services;

use App\Mail\AdminInquiryReceived;
use App\Models\DemoRequests;
use App\Models\EventSupport;
use App\Models\Setting;
use App\Models\Support;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AdminInquiryAlertService
{
    public function notifySupportRequest(Support $support): void
    {
        $this->notify(
            source: 'contact form',
            title: 'New contact form submission',
            body: $support->subject,
            payload: [
                'name' => $support->name,
                'email' => $support->email,
                'phone' => $support->phone,
                'location' => $support->location,
                'message' => $support->description,
            ]
        );
    }

    public function notifyEventTicket(EventSupport $support): void
    {
        $eventTitle = $support->event?->title ?? 'an event';

        $this->notify(
            source: 'event support ticket',
            title: 'New event support ticket',
            body: $support->message,
            payload: [
                'event' => $eventTitle,
                'name' => $support->name,
                'email' => $support->email,
                'phone' => $support->phone,
                'message' => $support->message,
            ],
            eventId: $support->event_id
        );
    }

    public function notifyDemoRequest(DemoRequests $demo): void
    {
        $this->notify(
            source: 'demo request',
            title: 'New demo request',
            body: $demo->booking_date . ' at ' . $demo->time_slot,
            payload: [
                'name' => $demo->name ?: optional($demo->user)->name,
                'email' => $demo->email ?: optional($demo->user)->email,
                'phone' => $demo->phone,
                'booking date' => $demo->booking_date,
                'time slot' => $demo->time_slot,
                'timezone' => $demo->timezone,
            ]
        );
    }

    private function notify(
        string $source,
        string $title,
        string $body,
        array $payload,
        ?int $eventId = null
    ): void
    {
        $superAdmin = User::find(1);
        $recipient = $superAdmin?->email ?: Setting::where('key', 'support_email')->value('value');

        if ($superAdmin) {
            notification(
                1,
                $source,
                null,
                $title,
                $body,
                $eventId
            );
        }

        if (!filled($recipient)) {
            return;
        }

        Mail::to($recipient)->send(new AdminInquiryReceived($title, $payload));
    }
}
