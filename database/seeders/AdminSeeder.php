<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::findOrNew(1);
        $user->name = 'Super';
        $user->lastname = 'Admin';
        $user->email = 'admin@admin.com';
        $user->mobile = '12345678';
        $user->username = 'admin';
        $user->primary_group='Admin';
        $user->password = Hash::make('password');
        $user->save();
        $user->assignRole('Admin');
        
       $eventAdmin = User::create([
            'name' => 'Event',
            'lastname'=>'Admin',
            'primary_group'=>'Admin',
            'email' => 'event@admin.com',
            'mobile' => '123345678',
            'password' => Hash::make('password'),
        ]);
       
        $eventAdmin->assignRole('Admin');
        notification($eventAdmin->id,'welcome');
        qrCode($eventAdmin->id);


        $SupportStaff = User::create([
            'name' => 'Support Staff',
            'lastname'=>'Or Helpdesk',
            'email' => 'support@staff.com',
            'mobile' => '12345678',
            'primary_group'=>'Support Staff Or Helpdesk',
            'password' => Hash::make('password'),
        ]);
        $SupportStaff->assignRole('Support Staff Or Helpdesk');
        qrCode($SupportStaff->id); 
        notification($SupportStaff->id,'welcome');

        $registrationDesk = User::create([
            'name' => 'Registration',
            'lastname'=>'Desk',
            'email' => 'registration@desk.com',
            'mobile' => '12345678',
            'primary_group'=>'Registration Desk',
            'password' => Hash::make('password'),
        ]);
        $registrationDesk->assignRole('Registration Desk');
        qrCode($registrationDesk->id);
        notification($registrationDesk->id,'welcome');
    }
}
