<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Category;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $faker = Faker::create(); 
        // Create sample events if they don't exist
        if (Event::count() == 0) {
          
        $categoryIds = Category::pluck('id')->toArray();

        $events = [];


        $categoyName = getCategory("event,tags")->pluck('name')->toArray();
        // --- 7 past events ---
        for ($i = 1; $i <=1; $i++) {
            $start = now()->subDay();
            $end   = (clone $start)->addYears(100);

            $events[] = [
                'title' => "CanadianSME Small Business Summit 2025",
                'description' => 'Spark your ambition this October at the Metro Toronto Convention Centre, where vision meets action at the CanadianSME Small Business Summit 2025. Under the theme "AI-Driven Innovation: Empowering Canadian SMEs," this summit is your arena to revolutionize business strategies through the transformative power of AI. Explore an exhibition space teeming with cutting-edge solutions and seize unparalleled networking opportunities.',
                'location' => "North Building, Level 100, 255 Front Street West, Toronto, Ontario, M5V 2W6, Canada",
                'tags' => implode(',', $faker->randomElements(
                         $categoyName,
                        rand(1, 3)
                    )),
                'start_date' => $start,
                'end_date' => $end,
                'is_featured' => 1,
                'visibility' => 'public',
                'created_by' => 1,
                'status' => 'published',
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'youtube_link'=> "https://youtu.be/p2WUbF1dXus"
            ];
        }

   
        
            foreach ($events as $event) {
                Event::create(array_merge($event, [
                    'slug' => Str::slug($event['title'])
                ]));
            }
        }
    }
}
