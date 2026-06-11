<?php

namespace App\Mail;

use App\Models\TicketInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TicketInvoice $invoice)
    {
        $this->invoice->loadMissing('order.event', 'order.ticketType', 'order.attendeePurchases.user');
    }

    public function build()
    {
        $subject = 'Invoice ' . $this->invoice->invoice_number;

        if (!empty($this->invoice->order?->event?->title)) {
            $subject .= ' - ' . $this->invoice->order->event->title;
        }

        $mail = $this->subject($subject)
            ->view('emails.ticket_invoice')
            ->with([
                'invoice' => $this->invoice,
                'event' => $this->invoice->order?->event,
                'order' => $this->invoice->order,
                'attendees' => $this->invoice->order?->attendeePurchases
                    ?->map(fn ($purchase) => $purchase->user)
                    ->filter()
                    ->values() ?? collect(),
            ]);

        if ($this->invoice->pdf_path) {
            $mail->attach(storage_path('app/' . $this->invoice->pdf_path), [
                'as' => $this->invoice->invoice_number . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
