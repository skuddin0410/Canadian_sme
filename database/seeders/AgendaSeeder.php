<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Session;
use App\Models\UserAgenda;
use App\Models\FavoriteSession;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch users who are Speakers, Exhibitors, or Attendees
        $users = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Speaker', 'Exhibitor', 'Attendee']);
            })
            ->with('roles') // eager load roles
            ->get();
        $sessionIds = Session::pluck('id')->toArray();
        foreach ($users as $user) {
            $roleName = $user->roles->pluck('name')->first();
            foreach ($sessionIds as $sessionId) {
                // 50% chance to add agenda
                if (rand(0, 1)) {
                    addAgenda($sessionId, $roleName, $user->id);
                }

                // 50% chance to add favorite
                if (rand(0, 1)) {
                    addFavorite($sessionId, $user->id);
                }
            }
        }
    }

}
