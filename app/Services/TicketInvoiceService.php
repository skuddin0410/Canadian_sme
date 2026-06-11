<?php

namespace App\Services;

use App\Models\TicketInvoice;
use App\Models\TicketOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TicketInvoiceService
{
    public function createDraftForOrder(TicketOrder $order): TicketInvoice
    {
        return TicketInvoice::firstOrCreate(
            ['ticket_order_id' => $order->id],
            [
                'invoice_number' => $this->generateInvoiceNumber($order),
                'recipient_name' => $order->coordinator_name,
                'recipient_email' => $order->coordinator_email,
                'amount' => $order->total_amount,
                'currency' => strtoupper((string) ($order->currency ?: 'USD')),
            ]
        );
    }

    public function generateAndStore(TicketInvoice $invoice): TicketInvoice
    {
        $invoice->loadMissing([
            'order.event',
            'order.ticketType',
            'order.attendeePurchases.user',
        ]);

        $pdf = Pdf::loadView('invoices.ticket_order', [
            'invoice' => $invoice,
            'order' => $invoice->order,
            'pricingSummary' => $invoice->order->request['pricing_summary'] ?? [],
            'attendees' => $invoice->order->attendeePurchases
                ->map(fn ($purchase) => $purchase->user)
                ->filter()
                ->values(),
        ])->setPaper('a4');

        $relativePath = 'invoices/' . $invoice->invoice_number . '.pdf';
        Storage::disk('local')->put($relativePath, $pdf->output());

        $invoice->forceFill([
            'pdf_path' => $relativePath,
        ])->save();

        return $invoice->fresh('order.event', 'order.ticketType', 'order.attendeePurchases.user');
    }

    protected function generateInvoiceNumber(TicketOrder $order): string
    {
        $dateSegment = optional($order->created_at)->format('Ymd') ?: now()->format('Ymd');

        return 'INV-' . $dateSegment . '-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
    }
}
