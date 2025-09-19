<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\TicketCategory;
use App\Models\TicketType;
use App\Models\Event;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
       $categories = [
            [ 'name' => 'Leadership Summit', 'type' => 'event' ],
            [ 'name' => 'Startup & Investment', 'type' => 'event' ],
            [ 'name' => 'Digital Marketing', 'type' => 'event' ],
            [ 'name' => 'Sustainability & Environment', 'type' => 'event' ],
            [ 'name' => 'Blockchain & FinTech', 'type' => 'event' ],
            [ 'name' => 'E-commerce & Retail', 'type' => 'event' ],
            [ 'name' => 'Government & Policy', 'type' => 'event' ],
            [ 'name' => 'Women in Leadership', 'type' => 'event' ],
            [ 'name' => 'Event', 'type' => 'tags' ],
            [ 'name' => 'CloudTrends', 'type' => 'tags' ],
            [ 'name' => 'DataSecurity', 'type' => 'tags' ],
            [ 'name' => 'TechnoVation', 'type' => 'tags' ],
            [ 'name' => 'Gold','type' => 'sponsor', 'color' => '#FFD700' ], 
            [ 'name' => 'Majlislounge','type' => 'sponsor', 'color' => '#8B0000' ],
            [ 'name' => 'Platinum','type' => 'sponsor', 'color' => '#E5E4E2' ], 
            [ 'name' => 'Silver','type' => 'sponsor', 'color' => '#C0C0C0' ], 
            [ 'name' => 'Innovationpartner', 'type' => 'sponsor', 'color' => '#1E90FF' ],
            [ 'name' => 'Bronze','type' => 'sponsor', 'color' => '#CD7F32' ], 
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'type' => $category['type']
            ]);
        }

    }
}
