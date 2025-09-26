<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use App\Models\User; // make sure this matches your User model namespace

class GuestUserSeeder extends Seeder
{
    public function run(): void
    {   
        // Ensure Guest role exists
        $role = Role::firstOrCreate(['name' => 'Guest']); 

        $email = 'guest@guest.com';

        // Create or update user
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'       => 'guest',
                'lastname'   => 'user',
                'slug'       => 'guest-user',
                'primary_group'=>'Guest',
                'password'   => Hash::make('0000'),
                'is_approve' => 1,
            ]
        );

        // Assign role (avoid duplicates)
        if (! $user->hasRole($role->name)) {
            $user->assignRole($role);
        }

        // Insert/update OTP mapped by email; expire in 100 years
        $otp = '0000';
        DB::table('otps')->updateOrInsert(
            ['email' => $email],
            [
                'otp'        => $otp,
                'expired_at' => Carbon::now()->addYears(100),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $this->command->info("Guest user seeded with email {$email}, OTP {$otp} (expires in 100 years), and role '{$role->name}'.");
    }
}
