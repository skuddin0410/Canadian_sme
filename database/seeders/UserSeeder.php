<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
Use App\Models\Company;
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

        $companies = [];

        for ($i = 1; $i <= 10; $i++) {
            $name = "Company $i";

        $exhibitorAdmin = User::create([
            'name' => "Exhibitor$i",
            'lastname'=>'Admin',
            'email' => "exhibitor$i@admin.com",
            'mobile' => '12345678',
            'tags'=>'exhibitor,admin,event',
            'designation'=>'Event Manager',
            'bio'=>"This is a short bio for Exhibitor $i. Experienced in event management and exhibitions.",
            'password' => Hash::make('password')
        ]);
        $exhibitorAdmin->assignRole('Admin');
        qrCode($exhibitorAdmin->id);

            $companies= [
                'user_id'           => $exhibitorAdmin->id,
                'name'              => $name,
                'industry'          => ['Tech','Healthcare','Education','Finance','Entertainment'][array_rand(['Tech','Healthcare','Education','Finance','Entertainment'])],
                'size'              => ['1-10','11-50','51-200','201-500','500+'][array_rand(['1-10','11-50','51-200','201-500','500+'])],
                'location'          => "City $i, Country $i",
                'email'             => "info@company$i.com",
                'phone'             => "+1-555-000$i",
                'description'       => "This is a description for $name",
                'website'           => "https://www.company$i.com",
                'linkedin'          => "https://www.linkedin.com/company/company$i",
                'twitter'           => "https://twitter.com/company$i",
                'facebook'          => "https://facebook.com/company$i",
                'instagram'         => "https://instagram.com/company$i",
                'certifications'    => "ISO 900$i, ISO 140$i",
                'certification_image'=> "cert_$i.jpg",
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            $company=Company::create($companies); 

            $exhibitorRepresentative = User::create([
                'name' => "Exhibitor$i",
                'lastname'=>'Representative',
                'email' => "exhibitor$i@representative.com",
                'mobile' => '12345678',
                'password' => Hash::make('password'),
                'company_id'=>$company->id,
                'tags'=>'Representative, Exhibitor',
                'designation'=>'Representative',
                'bio'=>"This is a short bio for Representative $i. Experienced in event management and exhibitions.",
            ]);
            $exhibitorRepresentative->assignRole('Representative');

            qrCode($exhibitorRepresentative->id);

            $attendee = User::create([
                'name' => 'Attendee',
                'lastname'=>'Attendee',
                'email' => "attendee$i@attendee.com",
                'mobile' => '12345678',
                'password' => Hash::make('password'),
                'company_id'=>$company->id,
                'tags'=>'Attaindee',
                'designation'=>'Software Engineer',
                'bio'=>"This is a short bio for Attaindee $i. Experienced in event management and exhibitions.",
            ]);
            $attendee->assignRole('Attendee');
            qrCode($attendee->id);
            $exhibitorAdmin->company_id = $company->id;
            $exhibitorAdmin->save();
        }

        

        $speaker = User::create([
            'name' => 'Speaker',
            'lastname'=>'Speaker',
            'email' => 'speaker@speaker.com',
            'mobile' => '12345678',
            'password' => Hash::make('password'),
        ]);
        $speaker->assignRole('Speaker');
        qrCode($speaker->id);

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
