<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Company;

class ServicesSeeder extends Seeder
{
    public function run()
    {
        $categories = ServiceCategory::all();
        $companies = Company::all();
        foreach ($companies as $company) {
        foreach ($categories as $category) {
            // Create 3 services per category
            for ($i = 1; $i <= 3; $i++) {
                $name = "{$category->name} Service $i";
                Service::create([
                    'name' => $name,
                    'slug' => Str::slug($name).rand(100, 5000).rand(1,10).$category->id.$company->id,
                    'price' => rand(100, 5000),
                    'description' => "Detailed description for $name. This includes features, benefits, and other relevant information.",
                    'capabilities' => "Capability 1, Capability 2, Capability 3",
                    'deliverables' => "Deliverable 1, Deliverable 2, Deliverable 3",
                    'category_id' => $category->id,
                    'is_active' => rand(0,1),
                    'duration' => rand(1,10) . " hours",
                    'created_by' => $company->user_id,
                    'updated_by' => $company->user_id,
                    'company_id' => $company->id,
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
}

