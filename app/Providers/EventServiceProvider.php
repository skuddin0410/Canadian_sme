<?php

namespace App\Providers;

use App\Listeners\LogAdminAuthActivity;
use App\Listeners\LogFailedLogin;
use App\Listeners\LogSuccessfulLogin;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            LogSuccessfulLogin::class,
            LogAdminAuthActivity::class . '@handleLogin',
        ],
        Logout::class => [
            LogAdminAuthActivity::class . '@handleLogout',
        ],
        Failed::class => [
            LogFailedLogin::class,
            LogAdminAuthActivity::class . '@handleFailed',
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
