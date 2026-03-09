<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Models\FailedLogin;
use App\Models\GeneralNotification;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class LogFailedLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        $email = $event->credentials['email'] ?? ($event->credentials['username'] ?? 'unknown');
        $ip = Request::ip();
        $userAgent = Request::header('User-Agent');
        $time = now()->format('Y-m-d H:i:s');

        // 1. Log to failed_logins table
        FailedLogin::create([
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);

        // 2. Notify Admin(s)
        $admins = User::role('Admin')->get();
        if ($admins->isEmpty()) {
            // Fallback if no role found, try ID 1 as per isSuperAdmin helper
            $admin = User::find(1);
            if ($admin) {
                $admins = collect([$admin]);
            }
        }

        foreach ($admins as $admin) {
            GeneralNotification::create([
                'user_id' => $admin->id,
                'title' => 'Failed Login Attempt',
                'body' => "Failed login on {$email} at {$time}.",
                'related_type' => 'failed_login',
                'meta' => json_encode([
                    'email' => $email,
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                    'time' => $time
                ]),
                'is_read' => 0
            ]);
        }
    }
}
