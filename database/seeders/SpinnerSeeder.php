<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Spinner;

class SpinnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spinner = Spinner::findOrNew(1);
        $spinner->name = 'Lose';
        $spinner->type = 'none';
        $spinner->amount = 0.00;
        $spinner->coupon_id = null;
        $spinner->link = null;
        $spinner->description = null;
        $spinner->save();
    }
}
