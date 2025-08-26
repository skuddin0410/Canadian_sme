<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
Use App\Models\Company;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventAdmin = User::create([
            'name' => 'Event',
            'lastname'=>'Admin',
            'email' => 'event@admin.com',
            'mobile' => '123345678',
            'password' => Hash::make('password'),
        ]);
        $eventAdmin->assignRole('Admin');
         qrCode($eventAdmin->id);

        $exhibitorsNames = [
            ['first_name' => 'Liam',     'last_name' => 'Smith'],
            ['first_name' => 'Olivia',   'last_name' => 'Johnson'],
            ['first_name' => 'Noah',     'last_name' => 'Williams'],
            ['first_name' => 'Emma',     'last_name' => 'Brown'],
            ['first_name' => 'William',  'last_name' => 'Jones'],
            ['first_name' => 'Charlotte','last_name' => 'Garcia'],
            ['first_name' => 'Benjamin', 'last_name' => 'Miller'],
        ];

        $attendees =  [       
            ['first_name' => 'Amelia',   'last_name' => 'Davis'],
            ['first_name' => 'Lucas',    'last_name' => 'Martinez'],
            ['first_name' => 'Sophia',   'last_name' => 'Rodriguez'],
            ['first_name' => 'Ethan',    'last_name' => 'Wilson'],
            ['first_name' => 'Mia',      'last_name' => 'Anderson'],
            ['first_name' => 'James',    'last_name' => 'Taylor'],
            ['first_name' => 'Isabella', 'last_name' => 'Thomas'],
            ['first_name' => 'Logan',    'last_name' => 'Moore'],
            ['first_name' => 'Avery',    'last_name' => 'Martin'],
            ['first_name' => 'Jack',     'last_name' => 'Lavoie'],
            ['first_name' => 'Chloe',    'last_name' => 'Tremblay'],
            ['first_name' => 'Henry',    'last_name' => 'Roy'],
            ['first_name' => 'Émilie',   'last_name' => 'Gagnon'],
        ];

        $representativeNames = [
            ['first_name' => 'Oliver',   'last_name' => 'Ouellet'],
            ['first_name' => 'Zoé',      'last_name' => 'Bouchard'],
            ['first_name' => 'Nathan',   'last_name' => 'Fortin'],
            ['first_name' => 'Élodie',   'last_name' => 'Côté'],
            ['first_name' => 'Thomas',   'last_name' => 'Pelletier'],
            ['first_name' => 'Julien',   'last_name' => 'Beaulieu'],
        ];


        $sponsorsNames = [
            ['first_name' => 'Sarah',    'last_name' => 'Morin'],
            ['first_name' => 'Gabriel',  'last_name' => 'Lamoureux'],
            ['first_name' => 'Victoria', 'last_name' => 'Desjardins'],
            ['first_name' => 'Samuel',   'last_name' => 'Charbonneau'],
            ['first_name' => 'Madison',  'last_name' => 'Dubois'],
            ['first_name' => 'Logan',    'last_name' => 'Bélanger'],
            ['first_name' => 'Jacob',    'last_name' => 'Cloutier'],
            ['first_name' => 'Ella',     'last_name' => 'Girard'],
        ];

        $speakers = [
            ['first_name' => 'Aiden',    'last_name' => 'Lemieux'],
            ['first_name' => 'Hannah',   'last_name' => 'Carrier'],
            ['first_name' => 'Caleb',    'last_name' => 'Paquette'],
            ['first_name' => 'Leah',     'last_name' => 'Gauthier'],
            ['first_name' => 'Daniel',   'last_name' => 'Chevalier'],
            ['first_name' => 'Sophie',   'last_name' => 'Mercier'],
            ['first_name' => 'Ryan',     'last_name' => 'Blais'],
        ];

        $faker = Faker::create();
        $roles = ['Admin', 'Exhibitor', 'Sponsors', 'Representative', 'Attendee','Speaker'];
        $industries = ['Tech','Healthcare','Education','Finance','Entertainment'];
        $sizes      = ['1-10','11-50','51-200','201-500','500+'];

        foreach($exhibitorsNames as $exhibitor) {

            $exhibitorAdmin = User::create([
                'name' => $exhibitor['first_name'],
                'lastname'=>$exhibitor['last_name'],
                'email' => strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']) . '@example.com',
                'mobile' => $faker->numerify('##########'),
                'tags'=>implode(',', $faker->randomElements($roles, rand(1, 3))),
                'designation'=>$faker->jobTitle,
                'bio'=>$faker->paragraph(3),
                'password' => Hash::make('password'),
                'linkedin_url'            => "https://www.linkedin.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'twitter_url'             => "https://twitter.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'facebook_url'            => "https://facebook.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'instagram_url'           => "https://instagram.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'website_url'=>"https://example.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name'])
            ]);
            $exhibitorAdmin->assignRole('Exhibitor');
            qrCode($exhibitorAdmin->id);

            $companies = [
                'user_id'             => $exhibitorAdmin->id, // or assign dynamically
                'name'                => $faker->company,
                'industry'            => $faker->randomElement($industries),
                'size'                => $faker->randomElement($sizes),
                'location'            => $faker->city . ', ' . $faker->country,
                'email'               => $faker->companyEmail,
                'phone'               => $faker->e164PhoneNumber, // +14155552671 format
                'description'         => $faker->paragraph(3),
                'website'             => $faker->url,
                'linkedin'            => "https://www.linkedin.com/company/" . $faker->slug,
                'twitter'             => "https://twitter.com/" . $faker->slug,
                'facebook'            => "https://facebook.com/" . $faker->slug,
                'instagram'           => "https://instagram.com/" . $faker->slug,
                'certifications'      => "ISO " . $faker->numberBetween(9001, 9999) . ", ISO " . $faker->numberBetween(14001, 14999),
                'created_at'          => now(),
                'updated_at'          => now(),
            ];

            $company=Company::create($companies); 

            $exhibitorAdmin->company_id = $company->id;
            $exhibitorAdmin->save();

        }

        foreach ($sponsorsNames as $sponsor) {
            $sponsors = User::create([
                'name' => $sponsor['first_name'],
                'lastname'=>$sponsor['last_name'],
                'email' => strtolower($sponsor['first_name'] . '.' . $sponsor['last_name']) . '@example.com',
                'mobile' => $faker->numerify('##########'),
                'tags'=>implode(',', $faker->randomElements($roles, rand(1, 3))),
                'designation'=>$faker->jobTitle,
                'bio'=>$faker->paragraph(3),
                'password' => Hash::make('password'),
                'linkedin_url'            => "https://www.linkedin.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'twitter_url'             => "https://twitter.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'facebook_url'            => "https://facebook.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'instagram_url'           => "https://instagram.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'website_url'=>"https://example.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name'])
            ]);
             $sponsors->assignRole('Sponsors');
             qrCode($sponsors->id);

             $companies = [
                'user_id'             => $sponsors->id, // or assign dynamically
                'name'                => $faker->company,
                'industry'            => $faker->randomElement($industries),
                'size'                => $faker->randomElement($sizes),
                'location'            => $faker->city . ', ' . $faker->country,
                'email'               => $faker->companyEmail,
                'phone'               => $faker->e164PhoneNumber, // +14155552671 format
                'description'         => $faker->paragraph(3),
                'website'             => $faker->url,
                'linkedin'            => "https://www.linkedin.com/company/" . $faker->slug,
                'twitter'             => "https://twitter.com/" . $faker->slug,
                'facebook'            => "https://facebook.com/" . $faker->slug,
                'instagram'           => "https://instagram.com/" . $faker->slug,
                'certifications'      => "ISO " . $faker->numberBetween(9001, 9999) . ", ISO " . $faker->numberBetween(14001, 14999),
                'created_at'          => now(),
                'updated_at'          => now(),
            ];

            $company=Company::create($companies); 

            $sponsors->company_id = $company->id;
            $sponsors->save();

        }


        foreach ($speakers as $speakerVal) {

            $speaker = User::create([
                'name' => $speakerVal['first_name'],
                'lastname'=>$speakerVal['last_name'],
                'email' => strtolower($speakerVal['first_name'] . '.' . $speakerVal['last_name']) . '@example.com',
                'mobile' => $faker->numerify('##########'),
                'tags'=>implode(',', $faker->randomElements($roles, rand(1, 3))),
                'designation'=>$faker->jobTitle,
                'bio'=>$faker->paragraph(3),
                'password' => Hash::make('password'),
                'linkedin_url'            => "https://www.linkedin.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'twitter_url'             => "https://twitter.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'facebook_url'            => "https://facebook.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'instagram_url'           => "https://instagram.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'website_url'=>"https://example.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name'])
            ]);
            $speaker->assignRole('Speaker');
            qrCode($speaker->id);

            $companies = [
                'user_id'             => $speaker->id, // or assign dynamically
                'name'                => $faker->company,
                'industry'            => $faker->randomElement($industries),
                'size'                => $faker->randomElement($sizes),
                'location'            => $faker->city . ', ' . $faker->country,
                'email'               => $faker->companyEmail,
                'phone'               => $faker->e164PhoneNumber, // +14155552671 format
                'description'         => $faker->paragraph(3),
                'website'             => $faker->url,
                'linkedin'            => "https://www.linkedin.com/company/" . $faker->slug,
                'twitter'             => "https://twitter.com/" . $faker->slug,
                'facebook'            => "https://facebook.com/" . $faker->slug,
                'instagram'           => "https://instagram.com/" . $faker->slug,
                'certifications'      => "ISO " . $faker->numberBetween(9001, 9999) . ", ISO " . $faker->numberBetween(14001, 14999),
                'created_at'          => now(),
                'updated_at'          => now(),
            ];

            $company=Company::create($companies); 
            $speaker->company_id = $company->id;
            $speaker->save();

        }
           

        foreach ($representativeNames as $representativeVal) {

        $exhibitorRepresentative = User::create([
            'name' => $representativeVal['first_name'],
            'lastname'=>$representativeVal['last_name'],
            'email' => strtolower($representativeVal['first_name'] . '.' . $representativeVal['last_name']) . '@example.com',
            'mobile' => $faker->numerify('##########'),
            'tags'=>implode(',', $faker->randomElements($roles, rand(1, 3))),
            'designation'=>$faker->jobTitle,
            'bio'=>$faker->paragraph(3),
            'password' => Hash::make('password'),
            'linkedin_url'            => "https://www.linkedin.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'twitter_url'             => "https://twitter.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'facebook_url'            => "https://facebook.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'instagram_url'           => "https://instagram.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'website_url'=>"https://example.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name'])
        ]);
        $exhibitorRepresentative->assignRole('Representative');

        qrCode($exhibitorRepresentative->id);

           $companies = [
                'user_id'             => $exhibitorRepresentative->id, // or assign dynamically
                'name'                => $faker->company,
                'industry'            => $faker->randomElement($industries),
                'size'                => $faker->randomElement($sizes),
                'location'            => $faker->city . ', ' . $faker->country,
                'email'               => $faker->companyEmail,
                'phone'               => $faker->e164PhoneNumber, // +14155552671 format
                'description'         => $faker->paragraph(3),
                'website'             => $faker->url,
                'linkedin'            => "https://www.linkedin.com/company/" . $faker->slug,
                'twitter'             => "https://twitter.com/" . $faker->slug,
                'facebook'            => "https://facebook.com/" . $faker->slug,
                'instagram'           => "https://instagram.com/" . $faker->slug,
                'certifications'      => "ISO " . $faker->numberBetween(9001, 9999) . ", ISO " . $faker->numberBetween(14001, 14999),
                'created_at'          => now(),
                'updated_at'          => now(),
            ];

            $company=Company::create($companies); 
            $exhibitorRepresentative->company_id = $company->id;
            $exhibitorRepresentative->save();

        }

        foreach ($attendees as $attendeeVal) {
        $attendee = User::create([
                'name' => $attendeeVal['first_name'],
                'lastname'=>$attendeeVal['last_name'],
                'email' => strtolower($attendeeVal['first_name'] . '.' . $attendeeVal['last_name']) . '@example.com',
                'mobile' => $faker->numerify('##########'),
                'tags'=>implode(',', $faker->randomElements($roles, rand(1, 3))),
                'designation'=>$faker->jobTitle,
                'bio'=>$faker->paragraph(3),
                'password' => Hash::make('password'),
                'linkedin_url'            => "https://www.linkedin.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'twitter_url'             => "https://twitter.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'facebook_url'            => "https://facebook.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'instagram_url'           => "https://instagram.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name']),
                'website_url'=>"https://example.com/" . strtolower($exhibitor['first_name'] . '.' . $exhibitor['last_name'])
        ]);
        $attendee->assignRole('Attendee');
        qrCode($attendee->id);

           $companies = [
                'user_id'             => $attendee->id, // or assign dynamically
                'name'                => $faker->company,
                'industry'            => $faker->randomElement($industries),
                'size'                => $faker->randomElement($sizes),
                'location'            => $faker->city . ', ' . $faker->country,
                'email'               => $faker->companyEmail,
                'phone'               => $faker->e164PhoneNumber, // +14155552671 format
                'description'         => $faker->paragraph(3),
                'website'             => $faker->url,
                'linkedin'            => "https://www.linkedin.com/company/" . $faker->slug,
                'twitter'             => "https://twitter.com/" . $faker->slug,
                'facebook'            => "https://facebook.com/" . $faker->slug,
                'instagram'           => "https://instagram.com/" . $faker->slug,
                'certifications'      => "ISO " . $faker->numberBetween(9001, 9999) . ", ISO " . $faker->numberBetween(14001, 14999),
                'created_at'          => now(),
                'updated_at'          => now(),
            ];

            $company=Company::create($companies); 
            $attendee->company_id = $company->id;
            $attendee->save();

        }

       


        $SupportStaff = User::create([
            'name' => 'Support Staff',
            'lastname'=>'Or Helpdesk',
            'email' => 'support@staff.com',
            'mobile' => '12345678',
            'password' => Hash::make('password'),
        ]);
        $SupportStaff->assignRole('Support Staff Or Helpdesk');
        qrCode($SupportStaff->id); 
 
        $registrationDesk = User::create([
            'name' => 'Registration',
            'lastname'=>'Desk',
            'email' => 'registration@desk.com',
            'mobile' => '12345678',
            'password' => Hash::make('password'),
        ]);
        $registrationDesk->assignRole('Registration Desk');
        qrCode($registrationDesk->id);

    }
}
