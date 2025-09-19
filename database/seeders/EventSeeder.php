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
                'title' => "CanadianSME Small Business, ".$start->year,
                'description' => $faker->paragraph(3),
                'location' => $faker->address,
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
                'youtube_link'=> "https://www.youtube.com/watch?v=XTt1my9Hgto"
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
