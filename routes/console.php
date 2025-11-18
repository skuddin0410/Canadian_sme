<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\UpdateUserQrCodeJob;
use Illuminate\Console\Scheduling\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Artisan::command('qr:update-user-qr-codes', function (Schedule $schedule) {
    $schedule->call(function () {
       dispatch(new UpdateUserQrCodeJob()); // Dispatch the job manually
     })->everyMinute(); 
});