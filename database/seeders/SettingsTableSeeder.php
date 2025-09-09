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
            [
                'key' => 'email_subject',
                'value' => "Lorem Ipsum is simply",
            ],
            [
                'key' => 'email_content',
                'value' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
            ],
        ]);

        // Default Privacy Policy Text
        DB::table('settings')->insert([
            [
                'key' => 'privacy_policy',
                'value' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
            ],
        ]);

        // Default Terms & Conditions Text
        DB::table('settings')->insert([
            [
                'key' => 'terms_conditions',
                'value' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
            ],
        ]);

        // Default Thank You Page Message
        DB::table('settings')->insert([
            [
                'key' => 'thank_you_page',
                'value' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
            ],
        ]);

        // Default Thank You Page Message
        DB::table('settings')->insert([
            [
                'key' => 'about',
                'value' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
            ],
        ]);
    }
}

