<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\ProductCategory; // Make sure your model exists

class ProductsCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'All kinds of electronic items including gadgets, computers, and accessories.',
                'image_url' => 'https://via.placeholder.com/150',
                'is_active' => 1,
            ],
            [
                'name' => 'Fashion',
                'description' => 'Clothing, footwear, and accessories for men, women, and kids.',
                'image_url' => 'https://via.placeholder.com/150',
                'is_active' => 1,
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Products for home improvement, kitchen essentials, and decor.',
                'image_url' => 'https://via.placeholder.com/150',
                'is_active' => 1,
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Gear and equipment for sports, fitness, and outdoor activities.',
                'image_url' => 'https://via.placeholder.com/150',
                'is_active' => 1,
            ],
            [
                'name' => 'Books & Stationery',
                'description' => 'Books, notebooks, and stationery items for all ages.',
                'image_url' => 'https://via.placeholder.com/150',
                'is_active' => 1,
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::create(array_merge($category, [
                'slug' => Str::slug($category['name']),
            ]));
        }
    }
}
