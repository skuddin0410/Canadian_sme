<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class TicketInventoryLog extends Model
{
    use HasFactory;
    use  Auditable;
    use AutoHtmlDecode;

    protected $fillable = [
        'ticket_type_id', 'action', 'quantity', 'previous_quantity',
        'new_quantity', 'reason', 'metadata', 'user_id'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    // Relationships
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}