<?php
namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogFailedLogin;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            LogSuccessfulLogin::class,
        ],
        Failed::class => [
            LogFailedLogin::class,
        ],
    ];

    // Optionally disable auto-discovery
    // protected function shouldDiscoverEvents(): bool
    // {
    //     return true;
    // }
}
