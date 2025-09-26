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
          
            RoleSeeder::class, // php artisan db:seed --class=RoleSeeder
            AdminSeeder::class, // php artisan db:seed --class=AdminSeeder
            TrackSeeder::class,
            CategorySeeder::class,
            EventSeeder::class,
            TicketSeeder::class,
            SettingSeeder::class, // php artisan db:seed --class=SettingSeeder
            PagesSeeder::class, // php artisan db:seed --class=PagesSeeder
            InsertLocaions::class, // php artisan db:seed --class=InsertLocaions,
            SettingsTableSeeder::class,// php artisan db:seed --class=SettingsTableSeeder
            EmailTemplateSeeder::class, //php artisan db:seed --class=EmailTemplateSeeder
            FormsTableSeeder::class, //php artisan db:seed --class=FormsTableSeeder
            GuestUserSeeder::class,  //php artisan db:seed --class=GuestUserSeeder
            UpdateSlugsSeeder::class, //php artisan db:seed --class=UpdateSlugsSeeder
            //**********
            //AccountManagerPermissionSeeder::class,
            //UserSeeder::class, // Need to disabled php artisan db:seed --class=UserSeeder
            //TicketSystemSeeder::class, // php artisan db:seed --class=TicketSystemSeeder
            //SupportSeeder::class,
            //SessionAttendee::class,// php artisan db:seed --class=SessionAttendee
            //AddExhibitorUsers::class,
            //AgendaSeeder::class, // php artisan db:seed --class=AgendaSeeder,
            //AccessPemissionSeeder::class, // php artisan db:seed --class=AccessPemissionSeeder
            //UserConnectionSeeder::class ,// php artisan db:seed --class=UserConnectionSeeder
            //*****
            
            
        ]);
    }
}
