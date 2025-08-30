<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionAttendee extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessions = DB::table('event_sessions')->pluck('id')->toArray();
        $users = DB::table('users')->pluck('id')->toArray();

        foreach ($sessions as $sessionId) {
            $attendees = collect($users)->random(15);
            foreach ($attendees as $userId) {
                DB::table('session_attendees')->insert([
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                notification($userId,'Attendee',$sessionId);
                notification($userId,'Attendee_Reminder',$sessionId);
            }
        }
    }
}
