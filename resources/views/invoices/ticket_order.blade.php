<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #233042;
            font-size: 12px;
            line-height: 1.45;
        }
        .header, .section {
            width: 100%;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #002364;
        }
        .muted {
            color: #67758a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #d9e2ef;
            padding: 8px 10px;
            vertical-align: top;
        }
        th {
            background: #f4f7fb;
            text-align: left;
        }
        .summary td:first-child {
            width: 220px;
            font-weight: bold;
            background: #f8fafc;
        }
        .totals td:first-child {
            width: 220px;
        }
        .amount {
            text-align: right;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    @php
        $eventTitle = $order->event?->title ?? 'Event';
        $ticketName = $order->ticketType?->name ?? 'Ticket';
        $currency = strtoupper($invoice->currency ?: 'USD');
        $baseSubtotal = (float) ($pricingSummary['base_subtotal'] ?? $order->total_amount);
        $discount = (float) ($pricingSummary['savings'] ?? 0);
        $finalTotal = (float) $order->total_amount;
        $discountNotes = [];

        if (!empty($pricingSummary['is_early_bird_applied'])) {
            $discountNotes[] = ($pricingSummary['early_bird_units'] ?? 0) . ' early bird';
        }
        if (!empty($pricingSummary['is_group_discount_applied'])) {
            $discountNotes[] = 'group discount ' . number_format((float) ($pricingSummary['group_discount_percentage'] ?? 0), 2) . '%';
        }
    @endphp

    <table class="header">
        <tr>
            <td style="border:none; padding:0;">
                <div class="title">Invoice</div>
                <div class="muted">{{ $invoice->invoice_number }}</div>
            </td>
            <td style="border:none; padding:0; text-align:right;">
                <strong>{{ $eventTitle }}</strong><br>
                <span class="muted">Issued: {{ optional($invoice->created_at)->format('d M Y, h:i A') }}</span><br>
                <span class="muted">Payment Ref: {{ $order->payment_reference ?: 'N/A' }}</span>
            </td>
        </tr>
    </table>

    <table class="section summary">
        <tr>
            <td>Billed To</td>
            <td>
                {{ $invoice->recipient_name ?: 'Participant' }}<br>
                {{ $invoice->recipient_email ?: 'N/A' }}
            </td>
        </tr>
        <tr>
            <td>Ticket</td>
            <td>{{ $ticketName }}</td>
        </tr>
        <tr>
            <td>Attendee Count</td>
            <td>{{ $order->attendee_count }}</td>
        </tr>
        <tr>
            <td>Registration Type</td>
            <td>{{ ucfirst((string) ($order->request['registration_mode'] ?? 'single')) }}</td>
        </tr>
    </table>

    <table class="section">
        <thead>
            <tr>
                <th>Description</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $ticketName }} x {{ $order->attendee_count }}</td>
                <td class="amount">{{ $currency }} {{ number_format($baseSubtotal, 2) }}</td>
            </tr>
            @if($discount > 0)
            <tr>
                <td>
                    Discount
                    @if(!empty($discountNotes))
                        <span class="muted">({{ implode(', ', $discountNotes) }})</span>
                    @endif
                </td>
                <td class="amount">- {{ $currency }} {{ number_format($discount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>Total Paid</strong></td>
                <td class="amount"><strong>{{ $currency }} {{ number_format($finalTotal, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <table class="section">
        <thead>
            <tr>
                <th>Attendee</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendees as $attendee)
            <tr>
                <td>{{ trim(($attendee->name ?? '') . ' ' . ($attendee->lastname ?? '')) ?: 'N/A' }}</td>
                <td>{{ $attendee->email ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2">No attendee records were found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
