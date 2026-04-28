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

        // 2. Identify recipients
        $recipients = collect();

        // Always include Super Admin (ID 1) as they should see all failures
        $superAdmin = User::find(1);
        if ($superAdmin) {
            $recipients->push($superAdmin);
        }

        // Include the target user if they exist and are not the Super Admin
        // The event might already have the user, otherwise we look them up
        $targetUser = $event->user;
        if (!$targetUser && $email !== 'unknown') {
            $targetUser = User::where('email', $email)->orWhere('username', $email)->first();
        }

        if ($targetUser && $targetUser->id !== 1) {
            // Only notify if it's an Event Admin or another relevant user
            // Requirement: "event admin failed notification will only be seen by that respective event admin"
            $recipients->push($targetUser);
        }

        // Ensure unique recipients
        $recipients = $recipients->unique('id');

        foreach ($recipients as $recipient) {
            GeneralNotification::create([
                'user_id' => $recipient->id,
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
