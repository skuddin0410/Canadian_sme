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
        $user->password = Hash::make('password');
        $user->save();
        $user->assignRole('Admin');
        qrCode($user->id);
    }
}
