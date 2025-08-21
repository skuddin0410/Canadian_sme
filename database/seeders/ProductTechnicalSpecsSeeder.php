<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductTechnicalSpec;

class ProductTechnicalSpecsSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();

        foreach ($products as $product) {
            // Create 3-5 technical specs per product
            $specs = [
                ['spec_name' => 'Weight', 'spec_unit' => 'kg', 'spec_category' => 'General'],
                ['spec_name' => 'Height', 'spec_unit' => 'cm', 'spec_category' => 'Dimensions'],
                ['spec_name' => 'Width', 'spec_unit' => 'cm', 'spec_category' => 'Dimensions'],
                ['spec_name' => 'Battery Life', 'spec_unit' => 'hours', 'spec_category' => 'Performance'],
                ['spec_name' => 'Color', 'spec_unit' => '', 'spec_category' => 'Appearance'],
                ['spec_name' => 'Material', 'spec_unit' => '', 'spec_category' => 'Material'],
            ];

            $count = rand(3, 5); // number of specs per product
            $selectedSpecs = collect($specs)->random($count);

            foreach ($selectedSpecs as $spec) {
                ProductTechnicalSpec::create([
                    'product_id'   => $product->id,
                    'spec_name'    => $spec['spec_name'],
                    'spec_value'   => rand(1,100), // random value
                    'spec_unit'    => $spec['spec_unit'],
                    'spec_category'=> $spec['spec_category'],
                ]);
            }
        }
    }
}

