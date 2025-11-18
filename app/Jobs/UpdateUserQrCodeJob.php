<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\QrCodeService;  // Assuming you have a service for generating the QR code
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateUserQrCodeJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public function handle()
    {
        $users = User::whereNull('qr_code')->orWhere('qr_code', '')->get();

        foreach ($users as $user) {
            if (empty($user->qr_code)) {
                qrCode($user->id, 'user');
            }

            // Assign role if not already assigned
            if (!$user->hasRole('Attendee')) {
                $user->assignRole('Attendee');
            }
        }
    }
}
