<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;

class FormsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('forms')->insert([
            'id' => 1,
            'title' => 'Create Your Account',
            'description' => "Sign up to start enjoying all the features. It's quick and easy!",
            'form_data' => json_encode([
                [
                    "max" => null,
                    "min" => null,
                    "type" => "text",
                    "is_delete_able" => 0,
                    "label" => "First Name",
                    "options" => [],
                    "validation" => ["required"],
                    "conditional_logic" => [
                        "value" => null,
                        "operator" => "==",
                        "condition" => "none",
                        "source_field" => null,
                        "target_field" => "First Name"
                    ]
                ],
                [
                    "max" => null,
                    "min" => null,
                    "type" => "text",
                    "is_delete_able" => 0,
                    "label" => "Last Name",
                    "options" => [],
                    "validation" => ["required"],
                    "conditional_logic" => [
                        "value" => null,
                        "operator" => "==",
                        "condition" => "none",
                        "source_field" => null,
                        "target_field" => "First Name"
                    ]
                ],
                [
                    "max" => null,
                    "min" => null,
                    "type" => "text",
                    "is_delete_able" => 0,
                    "label" => "Email",
                    "options" => [],
                    "validation" => ["required"],
                    "conditional_logic" => [
                        "value" => null,
                        "operator" => "==",
                        "condition" => "none",
                        "source_field" => null,
                        "target_field" => "Email"
                    ]
                ],
                [
                    "max" => null,
                    "min" => null,
                    "type" => "text",
                    "is_delete_able" => 0,
                    "label" => "Company",
                    "options" => [],
                    "validation" => [],
                    "conditional_logic" => [
                        "value" => null,
                        "operator" => "==",
                        "condition" => "none",
                        "source_field" => null,
                        "target_field" => "Company"
                    ]
                ],
                [
                    "max" => null,
                    "min" => null,
                    "type" => "text",
                    "is_delete_able" => 0,
                    "label" => "Designation",
                    "options" => [],
                    "validation" => [],
                    "conditional_logic" => [
                        "value" => null,
                        "operator" => "==",
                        "condition" => "none",
                        "source_field" => null,
                        "target_field" => "Designation"
                    ]
                ],
                [
                    "max" => null,
                    "min" => null,
                    "type" => "textarea",
                    "is_delete_able" => 0,
                    "label" => "Bio",
                    "options" => [],
                    "validation" => ["required"],
                    "conditional_logic" => [
                        "value" => null,
                        "operator" => "==",
                        "condition" => "none",
                        "source_field" => null,
                        "target_field" => "Bio"
                    ]
                ]
            ]),
            'validation_rules' => null,
            'conditional_logic' => null,
            'is_active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
