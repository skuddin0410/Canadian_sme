<?php

namespace Database\Seeders;

// use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TrackSeeder::class,
            RoleSeeder::class, // php artisan db:seed --class=RoleSeeder
            AdminSeeder::class, // php artisan db:seed --class=AdminSeeder
            UserSeeder::class,
            SettingSeeder::class, // php artisan db:seed --class=SettingSeeder
            AccountManagerPermissionSeeder::class,
            TicketSystemSeeder::class, // php artisan db:seed --class=TicketSystemSeeder
            PagesSeeder::class,
            ProductsCategoriesSeeder::class,
            ProductsSeeder::class,
            ProductTechnicalSpecsSeeder::class,
            ServiceCategoriesSeeder::class,
            ServicesSeeder::class,
            SettingsTableSeeder::class,// php artisan db:seed --class=SettingsTableSeeder
        ]);
    }
}
