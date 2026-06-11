<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketInvoice extends Model
{
    protected $fillable = [
        'ticket_order_id',
        'invoice_number',
        'recipient_name',
        'recipient_email',
        'amount',
        'currency',
        'pdf_path',
        'sent_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'sent_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(TicketOrder::class, 'ticket_order_id');
    }
}
