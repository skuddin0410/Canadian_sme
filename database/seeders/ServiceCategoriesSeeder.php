<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\ServiceCategory; // Make sure model exists

class ServiceCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Consulting',
                'description' => 'Professional consulting services to help businesses grow and succeed.',
                'image_url' => 'https://via.placeholder.com/150',
            ],
            [
                'name' => 'Digital Marketing',
                'description' => 'Services including SEO, social media management, and online advertising.',
                'image_url' => 'https://via.placeholder.com/150',
            ],
            [
                'name' => 'IT Support',
                'description' => 'Technical support services for businesses and individuals.',
                'image_url' => 'https://via.placeholder.com/150',
            ],
            [
                'name' => 'Event Management',
                'description' => 'Planning and managing corporate and social events.',
                'image_url' => 'https://via.placeholder.com/150',
            ],
            [
                'name' => 'Training & Workshops',
                'description' => 'Professional training and skill development workshops.',
                'image_url' => 'https://via.placeholder.com/150',
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::create(array_merge($category, [
                'slug' => Str::slug($category['name']),
            ]));
        }
    }
}

