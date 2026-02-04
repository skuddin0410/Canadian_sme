<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventAndEntityLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->select('id')->get();
        $companies = DB::table('companies')->select('id')->get();
        $speakers = DB::table('speakers')->select('id')->get();

        foreach ($users as $user) {
            DB::table('event_and_entity_link')->insert([
                'event_id' => 1,
                'entity_type' => 'users',
                'entity_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($companies as $company) {
            DB::table('event_and_entity_link')->insert([
                'event_id' => 1,
                'entity_type' => 'companies',
                'entity_id' => $company->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($speakers as $speaker) {
            DB::table('event_and_entity_link')->insert([
                'event_id' => 1,
                'entity_type' => 'speakers',
                'entity_id' => $speaker->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
