<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\UserLogin;


class LogSuccessfulLogin
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
    public function handle(Login $event): void
    {
        UserLogin::create([
            'user_id'     => $event->user->id,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->header('User-Agent'),
            'logged_in_at'=> now(),
        ]);
    }
}
