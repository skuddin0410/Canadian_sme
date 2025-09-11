<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Support;

class SupportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        Support::create([
            'subject'     => 'Login Issue',
            'description' => 'I am unable to login to my account. Please help.',
        ]);

        Support::create([
            'subject'     => 'Password Reset',
            'description' => 'I forgot my password and need assistance resetting it.',
        ]);

        Support::create([
            'subject'     => 'Feature Request',
            'description' => 'It would be great to have a dark mode feature in the app.',
        ]);

        Support::create([
            'subject'     => 'Bug Report',
            'description' => 'The sheet editor crashes when I try to upload an image.',
        ]);
    
    }
}
