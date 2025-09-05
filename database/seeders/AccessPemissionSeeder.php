<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccessPemissionSeeder extends Seeder
{
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            // Get speaker user IDs
            $speakerIds = \App\Models\User::whereHas('roles', function($q) {
                    $q->where('name', 'Speaker');
                })
                ->pluck('id')
                ->toArray();

            // Get exhibitor company IDs (from users who have a company_id)
            $exhibitorCompanyIds = \App\Models\Company::where('type', 0)
                ->pluck('id')
                ->toArray();
            // Get sponsor company IDs (assuming sponsors are stored in companies table with type='sponsor')
            $sponsorCompanyIds = \App\Models\Company::where('type', 1)
                ->pluck('id')
                ->toArray();

            // Example sessions loop
            $sessions =\App\Models\User::whereHas('roles', function($q) {
                    $q->whereIn('name', ['Speaker','Attendee','Exhibitor']);
                })->get();

            foreach ($sessions as $session) {
                // Randomly choose which field to update
                $choice = collect(['speaker', 'exhibitor', 'sponsor'])->random();

                if ($choice === 'speaker' && !empty($speakerIds)) {
                    $session->update([
                        'access_speaker_ids' => collect($speakerIds)->random(),
                        'access_exhibitor_ids' => null,
                        'access_sponsor_ids' => null,
                    ]);
                } elseif ($choice === 'exhibitor' && !empty($exhibitorCompanyIds)) {
                    $session->update([
                        'access_speaker_ids' => null,
                        'access_exhibitor_ids' => collect($exhibitorCompanyIds)->random(),
                        'access_sponsor_ids' => null,
                    ]);
                } elseif ($choice === 'sponsor' && !empty($sponsorCompanyIds)) {
                    $session->update([
                        'access_speaker_ids' => null,
                        'access_exhibitor_ids' => null,
                        'access_sponsor_ids' => collect($sponsorCompanyIds)->random(),
                    ]);
                }
            }
        }

}
