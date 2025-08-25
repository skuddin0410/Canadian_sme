<?php
namespace Database\Seeders;


use App\Models\{GeneralNotification, User};
use Illuminate\Database\Seeder;


class GeneralNotificationSeeder extends Seeder
{
public function run(): void
{
	if (User::count() === 0) {
	   User::factory()->count(2)->create();
	}


$alice = User::first();


	// 1) Simple broadcast (no user_id) about app update
	GeneralNotification::create([
		'user_id' => null,
		'title' => 'App Update Available',
		'body' => 'Version 2.3 brings performance improvements and dark mode.',
		'related_type' => null,
		'related_id' => null,
		'related_name' => null,
		'scheduled_at' => now()->addHours(2),
		'delivered_at' => null,
		'meta' => ['level' => 'info'],
	]);



	$bookingId = 123; // demo id
	GeneralNotification::create([
		'user_id' => $alice->id,
		'title' => 'Booking Confirmed',
		'body' => 'Your booking has been confirmed. Tap to view details.',
		'related_type' => 'App\\Models\\Booking',
		'related_id' => $bookingId,
		'related_name' => 'Booking #'.$bookingId,
		'delivered_at' => now(),
		'meta' => ['badge' => 1, 'sound' => 'default'],
	]);


    $eventId = 45; // demo id
	GeneralNotification::create([
		'user_id' => $alice->id,
		'title' => 'Event Reminder',
		'body' => 'Laravel Summit starts tomorrow at 10:00 AM.',
		'related_type' => 'App\\Models\\Event',
		'related_id' => $eventId,
		'related_name' => 'Laravel Summit',
		'scheduled_at' => now()->addDay()->setTime(8, 0),
		'meta' => ['priority' => 'high'],
	]);
    }
}