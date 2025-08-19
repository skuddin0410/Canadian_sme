<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;

class ServicesSeeder extends Seeder
{
    public function run()
    {
        $categories = ServiceCategory::all();
        $users = User::all();

        foreach ($categories as $category) {
            // Create 3 services per category
            for ($i = 1; $i <= 3; $i++) {
                $name = "{$category->name} Service $i";
                $createdBy = $users->random()->id;
                $updatedBy = $users->random()->id;

                Service::create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'price' => rand(100, 5000),
                    'description' => "Detailed description for $name. This includes features, benefits, and other relevant information.",
                    'capabilities' => "Capability 1, Capability 2, Capability 3",
                    'deliverables' => "Deliverable 1, Deliverable 2, Deliverable 3",
                    'category_id' => $category->id,
                    'is_active' => rand(0,1),
                    'duration' => rand(1,10) . " hours",
                    'created_by' => $createdBy,
                    'updated_by' => $updatedBy,
                    'image_url' => 'https://via.placeholder.com/300',
                    'gallery_images' => json_encode([
                        'https://via.placeholder.com/301',
                        'https://via.placeholder.com/302',
                        'https://via.placeholder.com/303',
                    ]),
                ]);
            }
        }
    }
}

