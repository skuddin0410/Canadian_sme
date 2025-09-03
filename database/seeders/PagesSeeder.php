<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Page;

class PagesSeeder extends Seeder
{
    public function run()
    {
            $pages = [];

            $fixedPages = [
                'Landing'  => 'landing',
                'About'    => 'about',
                'Location' => 'location',
                'Privacy'  => 'privacy',
                'Terms'    => 'terms',
            ];

            foreach ($fixedPages as $name => $slug) {
                $pages[] = [
                    'name'        => $name,
                    'slug'        => $slug, // fixed slug
                    'tags'        => implode(',', ['page', strtolower($name)]),
                    'description' =>'', 
                    'created_by'  => 1, // assign to random user
                    'status'      => ['draft', 'published', 'archived'][array_rand(['draft','published','archived'])],
                    'start_date'  => now()->subDays(rand(1, 10)),
                    'end_date'    => now()->addDays(rand(1, 10)),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }

            // Batch insert for performance
            Page::insert($pages);
    }
}

