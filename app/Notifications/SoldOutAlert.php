<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\TicketType;

class SoldOutAlert extends Notification
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
        return (new MailMessage)
            ->subject('SOLD OUT: ' . $this->ticketType->name)
            ->line('The ticket type "' . $this->ticketType->name . '" has sold out!')
            ->line('Event: ' . $this->ticketType->event->name)
            ->line('All ' . $this->ticketType->total_quantity . ' tickets have been sold.')
            ->action('View Inventory', route('admin.ticket-inventory.index'))
            ->line('Consider adding more inventory if needed.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Ticket Sold Out',
            'message' => 'Ticket type "' . $this->ticketType->name . '" has sold out!',
            'ticket_type_id' => $this->ticketType->id,
            'total_quantity' => $this->ticketType->total_quantity,
            'action_url' => route('admin.ticket-inventory.index'),
        ];
    }
}