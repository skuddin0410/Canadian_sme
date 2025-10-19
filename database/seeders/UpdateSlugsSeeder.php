<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateSlugsSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        DB::table('users')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
              $slug = createUniqueSlug('users', $user->name.'-'.$user->lastname,'slug', $user->id);
               DB::table('users')->where('id', $user->id)->update(['slug' => $slug]);
               DB::table('users')->where('id', $user->id)->update(['primary_group' => 'Attendee']);
            }
        });

        // Speakers
       /* DB::table('speakers')->orderBy('id')->chunk(100, function ($speakers) {
            foreach ($speakers as $speaker) {
                $slug = createUniqueSlug('speakers', $speaker->name.'-'.$speaker->lastname, 'slug',$speaker->id);
                DB::table('speakers')->where('id', $speaker->id)->update(['slug' => $slug]);
               
            }
        });

        // Companies
        DB::table('companies')->orderBy('id')->chunk(100, function ($companies) {
            foreach ($companies as $company) {
             $slug = createUniqueSlug('companies', $company->name,'slug', $company->id);
             DB::table('companies')->where('id', $company->id)->update(['slug' => $slug]);
            }
        });

        // Sessions
        DB::table('event_sessions')->orderBy('id')->chunk(100, function ($sessions) {
            foreach ($sessions as $session) {
               $slug =  createUniqueSlug('event_sessions', $session->title,'slug', $session->id);
               DB::table('event_sessions')->where('id', $session->id)->update(['slug' => $slug]);
            }
        });*/
    }
}
