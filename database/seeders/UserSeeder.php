<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventAdmin = User::create([
            'name' => 'Event',
            'lastname'=>'Admin',
            'email' => 'event@admin.com',
            'mobile' => '123345678',
            'password' => Hash::make('password'),
        ]);
        $eventAdmin->assignRole('Event Admin');


        $exhibitorAdmin = User::create([
            'name' => 'Exhibitor',
            'lastname'=>'Admin',
            'email' => 'exhibitor@admin.com',
            'mobile' => '12345678',
            'password' => Hash::make('password'),
        ]);
        $exhibitorAdmin->assignRole('Exhibitor Admin');

        $exhibitorRepresentative = User::create([
            'name' => 'Exhibitor',
            'lastname'=>'Representative',
            'email' => 'exhibitor@representative.com',
            'mobile' => '12345678',
            'password' => Hash::make('password'),
        ]);
        $exhibitorRepresentative->assignRole('Exhibitor Representative');

        $attendee = User::create([
            'name' => 'Attendee',
            'lastname'=>'Attendee',
            'email' => 'attendee@attendee.com',
            'mobile' => '12345678',
            'password' => Hash::make('password'),
        ]);
        $attendee->assignRole('Attendee');

        $speaker = User::create([
            'name' => 'Speaker',
            'lastname'=>'Speaker',
            'email' => 'speaker@speaker.com',
            'mobile' => '12345678',
            'password' => Hash::make('password'),
        ]);
        $speaker->assignRole('Speaker');

        $SupportStaff = User::create([
            'name' => 'Support Staff',
            'lastname'=>'Or Helpdesk',
            'email' => 'support@staff.com',
            'mobile' => '12345678',
            'password' => Hash::make('password'),
        ]);
        $SupportStaff->assignRole('Support Staff Or Helpdesk');


        $registrationDesk = User::create([
            'name' => 'Registration',
            'lastname'=>'Desk',
            'email' => 'registration@desk.com',
            'mobile' => '12345678',
            'password' => Hash::make('password'),
        ]);
        $registrationDesk->assignRole('Registration Desk');


    }
}
