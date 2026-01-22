<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Exhibitor']);
        // Role::firstOrCreate(['name' => 'Sponsors']);
        Role::firstOrCreate(['name' => 'Sponsor']);
        Role::firstOrCreate(['name' => 'Representative']);
        Role::firstOrCreate(['name' => 'Attendee']);
        Role::firstOrCreate(['name' => 'Speaker']);
        Role::firstOrCreate(['name' => 'Support Staff Or Helpdesk']);
        Role::firstOrCreate(['name' => 'Registration Desk']);
    }
}