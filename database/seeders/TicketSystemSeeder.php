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
        $categories = [
            ['name' => 'General Admission', 'color' => '#007bff', 'description' => 'Standard access tickets'],
            ['name' => 'VIP', 'color' => '#ffc107', 'description' => 'Premium access with additional benefits'],
            ['name' => 'Student', 'color' => '#28a745', 'description' => 'Discounted tickets for students'],
            ['name' => 'Early Bird', 'color' => '#17a2b8', 'description' => 'Limited time discount tickets'],
            ['name' => 'Group', 'color' => '#6f42c1', 'description' => 'Bulk purchase discounts'],
        ];

        foreach ($categories as $index => $categoryData) {
            TicketCategory::create([
                'name' => $categoryData['name'],
                'slug' => \Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'color' => $categoryData['color'],
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }

    $categories = [
        [ 'name' => 'Technology', 'type' => 'event' ],
        [ 'name' => 'Health & Wellness', 'type' => 'event' ],
        [ 'name' => 'Business & Entrepreneurship', 'type' => 'event' ],
        [ 'name' => 'Education & Training', 'type' => 'event' ],
        [ 'name' => 'Arts & Culture', 'type' => 'event' ],
        [ 'name' => 'Sports & Fitness', 'type' => 'event' ],
        [ 'name' => 'Music & Entertainment', 'type' => 'event' ],
        [ 'name' => 'Science & Innovation', 'type' => 'event' ],

        // 🔹 Summit / Conference related
        [ 'name' => 'Leadership Summit', 'type' => 'event' ],
        [ 'name' => 'Startup & Investment', 'type' => 'event' ],
        [ 'name' => 'Digital Marketing', 'type' => 'event' ],
        [ 'name' => 'Sustainability & Environment', 'type' => 'event' ],
        [ 'name' => 'AI & Machine Learning', 'type' => 'event' ],
        [ 'name' => 'Blockchain & FinTech', 'type' => 'event' ],
        [ 'name' => 'E-commerce & Retail', 'type' => 'event' ],
        [ 'name' => 'Government & Policy', 'type' => 'event' ],
        [ 'name' => 'Women in Leadership', 'type' => 'event' ],
        [ 'name' => 'Global Innovation Forum', 'type' => 'event' ],
    ];

    $tracks = [
                [ 'name' => 'Keynotes & Vision',            'slug' => 'keynotes-vision',            'type' => 'track' ],
                [ 'name' => 'Leadership & Strategy',        'slug' => 'leadership-strategy',        'type' => 'track' ],
                [ 'name' => 'Startup & Investment',         'slug' => 'startup-investment',         'type' => 'track' ],
                [ 'name' => 'Product Management',           'slug' => 'product-management',         'type' => 'track' ],
                [ 'name' => 'Design & UX',                  'slug' => 'design-ux',                  'type' => 'track' ],
                [ 'name' => 'Engineering & Architecture',   'slug' => 'engineering-architecture',   'type' => 'track' ],
                [ 'name' => 'AI & Machine Learning',        'slug' => 'ai-ml',                      'type' => 'track' ],
                [ 'name' => 'Generative AI & LLMs',         'slug' => 'genai-llms',                 'type' => 'track' ],
                [ 'name' => 'Data & Analytics',             'slug' => 'data-analytics',             'type' => 'track' ],
                [ 'name' => 'MLOps & Model Ops',            'slug' => 'mlops-modelops',             'type' => 'track' ],
                [ 'name' => 'Cloud & Infrastructure',       'slug' => 'cloud-infrastructure',       'type' => 'track' ],
                [ 'name' => 'DevOps & Platform Eng',        'slug' => 'devops-platform',            'type' => 'track' ],
                [ 'name' => 'Cybersecurity & Privacy',      'slug' => 'cybersecurity-privacy',      'type' => 'track' ],
                [ 'name' => 'Blockchain & Web3',            'slug' => 'blockchain-web3',            'type' => 'track' ],
                [ 'name' => 'FinTech & Payments',           'slug' => 'fintech-payments',           'type' => 'track' ],
                [ 'name' => 'HealthTech & Bio',             'slug' => 'healthtech-bio',             'type' => 'track' ],
                [ 'name' => 'EdTech & Learning',            'slug' => 'edtech-learning',            'type' => 'track' ],
                [ 'name' => 'Retail & E-commerce',          'slug' => 'retail-ecommerce',           'type' => 'track' ],
                [ 'name' => 'SaaS & B2B Growth',            'slug' => 'saas-b2b-growth',            'type' => 'track' ],
                [ 'name' => 'Marketing & Growth',           'slug' => 'marketing-growth',           'type' => 'track' ],
                [ 'name' => 'Sales & GTM',                  'slug' => 'sales-gtm',                  'type' => 'track' ],
                [ 'name' => 'Customer Success',             'slug' => 'customer-success',           'type' => 'track' ],
                [ 'name' => 'Sustainability & Climate',     'slug' => 'sustainability-climate',     'type' => 'track' ],
                [ 'name' => 'GovTech & Policy',             'slug' => 'govtech-policy',             'type' => 'track' ],
                [ 'name' => 'IoT & Edge',                   'slug' => 'iot-edge',                   'type' => 'track' ],
                [ 'name' => 'Robotics & Automation',        'slug' => 'robotics-automation',        'type' => 'track' ],
                [ 'name' => 'AR/VR & Spatial',              'slug' => 'ar-vr-spatial',              'type' => 'track' ],
                [ 'name' => 'Mobile & Frontend',            'slug' => 'mobile-frontend',            'type' => 'track' ],
                [ 'name' => 'Open Source',                  'slug' => 'open-source',                'type' => 'track' ],
                [ 'name' => 'Women in Tech & DEI',          'slug' => 'women-tech-dei',             'type' => 'track' ],
            ];

         foreach ($tracks as $track) {
            Track::create([
                'name' => $track,
                'slug' => Str::slug(implode('-',$track))
            ]);
        }

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'type' => 'event'
            ]);
        }

        // Create sample events if they don't exist
        if (Event::count() == 0) {
          
        $categoryIds = Category::pluck('id')->toArray();

        $events = [];

        // --- 7 past events ---
        for ($i = 1; $i <=1; $i++) {
            $start = now()->subDay();
            $end   = (clone $start)->addYears(100);

            $events[] = [
                'title' => "Canadian sme Summit ".$start->year,
                'description' => $faker->paragraph(3),
                'location' => $faker->address,
                'tags' => implode(',', $faker->randomElements(
                        ['past','event','future','conference','workshop','online','tech','health'],
                        rand(1, 3)
                    )),
                'start_date' => $start,
                'end_date' => $end,
                'is_featured' => 1,
                'visibility' => 'public',
                'created_by' => 1,
                'status' => 'published',
                'category_id' => $categoryIds[array_rand($categoryIds)],
            ];
        }

   
        
            foreach ($events as $event) {
                Event::create(array_merge($event, [
                    'slug' => Str::slug($event['title'])
                ]));
            }
        }

        $event = Event::first();
        $categories = TicketCategory::all();

        // Create ticket types
        $ticketTypes = [
            [
                'name' => 'General Admission',
                'category_id' => $categories->where('name', 'General Admission')->first()->id,
                'base_price' => 299.00,
                'total_quantity' => 500,
                'description' => 'Access to all conference sessions and networking events',
            ],
            [
                'name' => 'VIP Pass',
                'category_id' => $categories->where('name', 'VIP')->first()->id,
                'base_price' => 599.00,
                'total_quantity' => 100,
                'description' => 'Premium access with VIP lounge, priority seating, and exclusive sessions',
            ],
            [
                'name' => 'Student Ticket',
                'category_id' => $categories->where('name', 'Student')->first()->id,
                'base_price' => 149.00,
                'total_quantity' => 200,
                'description' => 'Discounted tickets for students with valid ID',
                'requires_approval' => true,
            ],
        ];

        foreach ($ticketTypes as $typeData) {
            $ticketType = TicketType::create([
                'event_id' => $event->id,
                'category_id' => $typeData['category_id'],
                'name' => $typeData['name'],
                'slug' => \Str::slug($typeData['name']),
                'description' => $typeData['description'],
                'base_price' => $typeData['base_price'],
                'total_quantity' => $typeData['total_quantity'],
                'available_quantity' => $typeData['total_quantity'],
                'min_quantity_per_order' => 1,
                'max_quantity_per_order' => 10,
                'is_active' => true,
                'requires_approval' => $typeData['requires_approval'] ?? false,
                'sale_start_date' => now()->subDays(30),
                'sale_end_date' => now()->addMonths(2),
            ]);
        }

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
                        ['Test1', 'Test2', 'Test3', 'Keynote', 'Workshop', 'Panel', 'Demo'],
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
        $users = DB::table('users')->pluck('id')->toArray();

        foreach ($sessions as $sessionId) {
            $speakers = collect($users)->random(2);

            foreach ($speakers as $userId) {
                DB::table('session_speakers')->insert([
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                    'role' => collect(['keynote','panelist','moderator'])->random(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach ($sessions as $sessionId) {
            $speakers = collect($users)->random(2);

            foreach ($speakers as $userId) {
                DB::table('session_exhibitors')->insert([
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach ($sessions as $sessionId) {
            $speakers = collect($users)->random(2);

            foreach ($speakers as $userId) {
                DB::table('session_sponsors')->insert([
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Ticket system seeded successfully!');
    }
}