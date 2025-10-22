<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateUserQrCode extends Seeder
{
    public function run(): void
    {   
        $users = User::whereNull('qr_code')->orWhere('qr_code', '')->get();
        foreach ($users as $user) {
            if(empty($user->qr_code)){
               qrCode($user->id, 'user'); 
            } 

            if (!$user->hasRole('Attendee')) {
              $user->assignRole('Attendee');
            } 
        }
    }
}
