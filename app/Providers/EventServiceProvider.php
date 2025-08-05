<?php
namespace App\Providers;

use Illuminate\Auth\Events\Login;
use App\Listeners\LogUserLogin;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            LogUserLogin::class,
        ],
    ];

    // Optionally disable auto-discovery
    // protected function shouldDiscoverEvents(): bool
    // {
    //     return true;
    // }
}
