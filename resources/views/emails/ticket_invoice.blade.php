@include('emails.layout.header')

<h3 style="margin-bottom:15px; color: #002364;">
    Invoice {{ $invoice->invoice_number }}
</h3>

<p style="margin:0 0 15px 0;">
    Your payment for <strong>{{ $event?->title ?? 'the event' }}</strong> was completed successfully.
    We have attached the invoice PDF to this email.
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: 0 0 20px 0;">
    <tbody>
        <tr>
            <td style="border:1px solid #dbe3f0; padding:10px; background:#f7f9fc; width:180px;"><strong>Invoice Number</strong></td>
            <td style="border:1px solid #dbe3f0; padding:10px;">{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #dbe3f0; padding:10px; background:#f7f9fc;"><strong>Ticket</strong></td>
            <td style="border:1px solid #dbe3f0; padding:10px;">{{ $order?->ticketType?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #dbe3f0; padding:10px; background:#f7f9fc;"><strong>Attendee Count</strong></td>
            <td style="border:1px solid #dbe3f0; padding:10px;">{{ $order?->attendee_count ?? 0 }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #dbe3f0; padding:10px; background:#f7f9fc;"><strong>Total Paid</strong></td>
            <td style="border:1px solid #dbe3f0; padding:10px;">{{ strtoupper($invoice->currency) }} {{ number_format((float) $invoice->amount, 2) }}</td>
        </tr>
    </tbody>
</table>

@if($attendees->isNotEmpty())
<p style="margin:0 0 10px 0;"><strong>Attendees</strong></p>
<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: 0 0 20px 0;">
    <thead>
        <tr>
            <th align="left" style="border:1px solid #dbe3f0; padding:10px; background:#f7f9fc;">Name</th>
            <th align="left" style="border:1px solid #dbe3f0; padding:10px; background:#f7f9fc;">Email</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendees as $attendee)
        <tr>
            <td style="border:1px solid #dbe3f0; padding:10px;">{{ trim(($attendee->name ?? '') . ' ' . ($attendee->lastname ?? '')) ?: 'N/A' }}</td>
            <td style="border:1px solid #dbe3f0; padding:10px;">{{ $attendee->email ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<p style="margin:0; font-size:13px; color:#777;">
    Keep this invoice for your records.
</p>

@include('emails.layout.footer')
