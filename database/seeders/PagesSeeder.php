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

        for ($i = 1; $i <= 10; $i++) {
            $name = "Page $i";

            $pages[] = [
                'name' => $name,
                'slug' => Str::slug($name),
                'tags' => implode(',', ['tag'.$i, 'page', 'content']),
                'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Page $i detailed description goes here. This can be multiple sentences providing long content for testing purposes. It can also include HTML if your application supports it.",
                'created_by' => rand(1,3), // assign to random user
                'status' => ['draft','published','archived'][array_rand(['draft','published','archived'])],
                'start_date' => now()->subDays(rand(1,10)),
                'end_date' => now()->addDays(rand(1,10)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Page::insert($pages); // Batch insert for performance
    }
}

