<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserConnectionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id')->toArray();

        // loop through users and randomly connect them
        foreach ($users as $senderId) {
            // pick random receivers (excluding self)
            $receivers = collect($users)
                ->reject(fn($id) => $id === $senderId)
                ->random(rand(3, 5)) // connect with 1-3 random users
                ->toArray();

            foreach ($receivers as $receiverId) {
                // use your helper to create connection if not exists
                userConnection($senderId, $receiverId);
            }
        }
    }
}
