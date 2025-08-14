<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\TicketType;

class LowStockAlert extends Notification
{
    use Queueable;

    protected $ticketType;

    public function __construct(TicketType $ticketType)
    {
        $this->ticketType = $ticketType;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $stockPercentage = $this->ticketType->total_quantity > 0 
            ? round(($this->ticketType->available_quantity / $this->ticketType->total_quantity) * 100) 
            : 0;

        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->ticketType->name)
            ->line('The ticket type "' . $this->ticketType->name . '" is running low on inventory.')
            ->line('Available: ' . $this->ticketType->available_quantity . ' out of ' . $this->ticketType->total_quantity)
            ->line('Stock level: ' . $stockPercentage . '%')
            ->action('Manage Inventory', route('admin.ticket-inventory.index'))
            ->line('Please consider increasing the inventory or monitoring sales closely.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Low Stock Alert',
            'message' => 'Ticket type "' . $this->ticketType->name . '" is running low on inventory.',
            'ticket_type_id' => $this->ticketType->id,
            'available_quantity' => $this->ticketType->available_quantity,
            'total_quantity' => $this->ticketType->total_quantity,
            'action_url' => route('admin.ticket-inventory.index'),
        ];
    }
}