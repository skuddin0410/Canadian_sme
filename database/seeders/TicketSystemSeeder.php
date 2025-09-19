<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketCategory;
use App\Models\TicketType;
use App\Models\TicketPricingRule;
use App\Models\Event;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Session;
use App\Models\Booth;
use App\Models\Track;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TicketSystemSeeder extends Seeder
{
    public function run()
    {
        // Create ticket categories
        $faker = Faker::create();

        $booths = [
            ['title' => 'Tech Innovations Booth', 'booth_number' => 'B101', 'size' => 'Large', 'location_preferences' => 'Near Entrance'],
            ['title' => 'AI Showcase Booth', 'booth_number' => 'B102', 'size' => 'Medium', 'location_preferences' => 'Center Hall'],
            ['title' => 'Health & Wellness Booth', 'booth_number' => 'B103', 'size' => 'Small', 'location_preferences' => 'Near Stage'],
            ['title' => 'Startup Pitch Booth', 'booth_number' => 'B104', 'size' => 'Medium', 'location_preferences' => 'Back Area'],
            ['title' => 'Education Expo Booth', 'booth_number' => 'B105', 'size' => 'Large', 'location_preferences' => 'Corner Zone'],
        ];


        foreach ($booths as $booth) {
            Booth::create($booth);
        }
        $events = Event::all();
        $boothIds = Booth::pluck('id')->toArray();
        $calendarColors = config('calendar.colors');
        foreach ($events as $event) {
            for ($i = 1; $i <= 30; $i++) {
                for ($slot = 1; $slot <= 3; $slot++) { // 3 sessions per day
                    $start = $event->start_date
                        ->copy()
                        ->addDays($i - 1)
                        ->setTime(9 + ($slot * 2), 0); // 11 AM, 1 PM, 3 PM
                    $end = $start->copy()->addHour();
                
                    Session::create([
                        'event_id'    => $event->id,
                        'booth_id'    => $boothIds[array_rand($boothIds)], // assign booth from DB
                        'title'       =>  $faker->randomElement([
                            'Future of Technology Summit',
                            'AI & Machine Learning Workshop',
                            'Digital Transformation Panel',
                            'Healthcare Innovation Forum',
                            'Women in Leadership Conference',
                            'Blockchain & FinTech Meetup',
                            'Global Sustainability Roundtable',
                            'E-commerce Growth Strategies',
                            'Startup & Investment Pitch Day',
                            'Creative Arts & Culture Expo',
                            'Sports Science Symposium',
                            'Music & Entertainment Festival',
                            'Education & Training Masterclass',
                            'Science & Innovation Showcase',
                            'Business & Entrepreneurship Bootcamp',
                        ]),
                        'location'       =>$faker->city . ', ' . $faker->country,
                        'description' => $faker->paragraph(1),
                        'keynote' => $faker->paragraph(1),
                        'demoes' => $faker->paragraph(1),
                        'panels' => $faker->paragraph(1),
                        'track'=>implode(',', $faker->randomElements(
                        ['Keynotes & Vision', 'Leadership & Strategy', 'Product Management', 'Engineering & Architecture', 'Generative AI & LLMs', 'Data & Analytics', 'MLOps & Model Ops','Cloud & Infrastructure'],
                        rand(1, 3)
                        )),
                        'color'=>$calendarColors[array_rand($calendarColors)],
                        'start_time'  => $start,
                        'end_time'    => $end,
                        'status'      => 'published',
                        'is_featured'=>rand(0, 1),
                        'type'        => ['session','workshop','keynote'][array_rand(['session','workshop','keynote'])],
                        'capacity'    => rand(50, 200),
                    ]);
                }
            }
        }

        $sessions = DB::table('event_sessions')->pluck('id')->toArray();
        $users = DB::table('users')->where('primary_group','Speaker')->pluck('id')->toArray();

        foreach ($sessions as $sessionId) {
            $speakers = collect($users)->random(4);

            foreach ($speakers as $userId) {
                DB::table('session_speakers')->insert([
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                    'role' => collect(['keynote','panelist','moderator'])->random(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                notification($userId,'Speaker_Reminder',$sessionId);
            }
        }
        
        
        $exhbitors = DB::table('companies')->where('is_sponsor',0)->pluck('id')->toArray();
        foreach ($sessions as $sessionId) {
            $speakers = collect($exhbitors)->random(5);

            foreach ($speakers as $userId) {
                DB::table('session_exhibitors')->insert([
                    'session_id' => $sessionId,
                    'company_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                notification($userId,'Exhibitor_Reminder',$sessionId);
            }
        }

        $sponsors = DB::table('companies')->where('is_sponsor',1)->pluck('id')->toArray();

        foreach ($sessions as $sessionId) {
            $speakers = collect($sponsors)->random(5);

            foreach ($speakers as $userId) {
                DB::table('session_sponsors')->insert([
                    'session_id' => $sessionId,
                    'company_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Ticket system seeded successfully!');
    }
}