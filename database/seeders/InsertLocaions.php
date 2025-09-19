<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use App\Models\Page;

class InsertLocaions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 

        $location = "
        Where to find us

        Cmarketing Inc  
        2800 Skymark Avenue, Suite 203  
        Mississauga, ON. Canada. L4W 5A6

        SK Uddin,  
        Organizer  
        sk@canadiansme.ca  
        647 668 5785

        Maheen Bari  
        info@canadiansme.ca  
        416 655 0205 / 647 254 0864

        Monday – Friday : 9:00 am – 5:00 pm
       ";
        DB::table('settings')->insert([
            [
                'key' => 'location',
                'value' => $location,
            ],
        ]);

        Page::where('slug','location')->update(['description' => $location]);
    }
}
