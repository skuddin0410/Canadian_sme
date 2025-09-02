<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;

class AddExhibitorUsers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::where('is_sponsor',0)->get();

        if ($companies->isEmpty()) {
            $this->command->warn('No companies found. Please create companies first.');
            return;
        }

        $exhibitors = User::role('Exhibitor')->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Exhibitor"]);
            })->get();

        foreach ($exhibitors as $user) {
            $company = $companies->random();
            $user->company_id = $company->id;
            $user->save();
            $this->command->info("Assigned {$user->name} to {$company->name}");
        }
    }
}
