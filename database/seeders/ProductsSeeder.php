<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Company;
use App\Models\User;
use App\Models\ProductCategory;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $companies = Company::all();
        $categories = ProductCategory::all();

        foreach ($companies as $company) {

            for ($i = 1; $i <= 3; $i++) {
                $category = $categories->random();
                $name = "Product $i for " . $company->name;

                Product::create([
                    'name'        => $name,
                    'slug'        => Str::slug($name),
                    'price'       => rand(50, 5000), // random price
                    'description' => "This is a detailed description for $name. It explains the product in detail and highlights its uses.",
                    'features'    => "Feature 1, Feature 2, Feature 3",
                    'benefits'    => "Benefit 1, Benefit 2, Benefit 3",
                    'category_id' => $category->id,
                    'user_id'     => $company->user_id,
                    'company_id'  => $company->id,
                    'is_active'   => rand(0,1),
                ]);
            }
        }
    }
}

