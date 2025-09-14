<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Support;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SupportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
       $user = User::first();

        if (!$user) {
            $this->command->warn('No users found in the users table. Please seed users first.');
            return;
        }

        Support::create([
            'subject'     => 'Login Issue',
            'description' => 'I am unable to login to my account. Please help.',
            'added_by'    => $user->id,
            'status'      => 'pending',
        ]);

        Support::create([
            'subject'     => 'Password Reset',
            'description' => 'I forgot my password and need assistance resetting it.',
            'added_by'    => $user->id,
            'status'      => 'inprogress',
        ]);

        Support::create([
            'subject'     => 'Feature Request',
            'description' => 'It would be great to have a dark mode feature in the app.',
            'added_by'    => $user->id,
            'status'      => 'pending',
        ]);

        Support::create([
            'subject'     => 'Bug Report',
            'description' => 'The sheet editor crashes when I try to upload an image.',
            'added_by'    => $user->id,
            'status'      => 'completed',
        ]);
    }
}
