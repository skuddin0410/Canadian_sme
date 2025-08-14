<?php
namespace App\Observers;

use App\Models\TicketType;
use App\Notifications\LowStockAlert;
use App\Notifications\SoldOutAlert;
use Illuminate\Support\Facades\Notification;

class TicketTypeObserver
{
    public function updated(TicketType $ticketType)
    {
        // Check for low stock alerts
        if ($ticketType->wasChanged('available_quantity')) {
            $this->checkStockAlerts($ticketType);
        }
    }

    protected function checkStockAlerts(TicketType $ticketType)
    {
        $lowStockThreshold = config('tickets.inventory.low_stock_threshold', 0.1);
        $stockPercentage = $ticketType->total_quantity > 0 
            ? $ticketType->available_quantity / $ticketType->total_quantity 
            : 0;

        // Send sold out notification
        if ($ticketType->available_quantity == 0 && config('tickets.notifications.sold_out_notification')) {
            $admins = \App\Models\User::whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })->get();
            
            Notification::send($admins, new SoldOutAlert($ticketType));
        }
        // Send low stock notification
        elseif ($stockPercentage <= $lowStockThreshold && config('tickets.notifications.low_stock_notification')) {
            $admins = \App\Models\User::whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })->get();
            
            Notification::send($admins, new LowStockAlert($ticketType));
        }
    }
}