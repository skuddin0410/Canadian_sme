<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateUserQrCode extends Seeder
{
    public function run(): void
    {   
        $users = User::get();
        foreach ($users as $user) {
            print_r($user);
            qrCode($user->id, 'user');
        }
    }
}
