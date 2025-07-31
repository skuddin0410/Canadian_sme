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
      'key' => 'referrer',
      'value' => 10.00,
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'contact_email',
      'value' => '',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'contact_phone',
      'value' => '',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'contact_whatsapp',
      'value' => '',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'contact_address',
      'value' => '',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'profile_referral',
      'value' => '',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'giveaway_on_top',
      'value' => 1,
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'quiz_on_top',
      'value' => 0,
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'home_page_giveaways',
      'value' => '/giveaways',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'home_page_quizzes',
      'value' => '/quizzes',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'home_page_spinners',
      'value' => '/spinners',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'home_page_link_1',
      'value' => '',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'home_page_link_2',
      'value' => '',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'home_page_link_3',
      'value' => '',
      'created_at' => now(),
      'updated_at' => now(),
    ], [
      'key' => 'home_page_link_4',
      'value' => '',
      'created_at' => now(),
      'updated_at' => now(),
    ]];
    Setting::upsert($settings, ['key'], ['value']);
  }
}
