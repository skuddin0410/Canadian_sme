<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $settings = [[
      'key' => 'color',
      'value' => '#0d6efd',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'logo',
      'value' => '',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'cover',
      'value' => '',
      'created_at' => now(),
      'updated_at' => now(),
    ]];
    Setting::upsert($settings, ['key'], ['value']);
  }
}
