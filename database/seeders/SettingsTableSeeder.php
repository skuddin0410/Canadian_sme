<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        // Default Organization Info
        DB::table('settings')->insert([
            [
                'key' => 'company_name',
                'value' => 'My Awesome Company',
            ],
            [
                'key' => 'company_address',
                'value' => '123 Main Street, City, Country',
            ],
            [
                'key' => 'support_email',
                'value' => 'support@mycompany.com',
            ],
            [
                'key' => 'tax_name',
                'value' => 'VAT',
            ],
            [
                'key' => 'tax_percentage',
                'value' => '15',
            ],
            [
                'key' => 'company_number',
                'value' => 'MYCIN123456',
            ],
        ]);

        // Default Privacy Policy Text
        DB::table('settings')->insert([
            [
                'key' => 'privacy_policy',
                'value' => 'This is the Privacy Policy. We value your privacy and protect your data.',
            ],
        ]);

        // Default Terms & Conditions Text
        DB::table('settings')->insert([
            [
                'key' => 'terms_conditions',
                'value' => 'These are the Terms and Conditions. Please read them carefully.',
            ],
        ]);

        // Default Thank You Page Message
        DB::table('settings')->insert([
            [
                'key' => 'thank_you_page',
                'value' => 'Thank you for your purchase! Your order number is {{ORDER_NO}}.',
            ],
        ]);
    }
}

