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
        

        $exhibitorsNames = [
            ['first_name' => 'Liam',     'last_name' => 'Smith'],
            ['first_name' => 'Olivia',   'last_name' => 'Johnson'],
            ['first_name' => 'Noah',     'last_name' => 'Williams'],
            ['first_name' => 'Emma',     'last_name' => 'Brown'],
            ['first_name' => 'William',  'last_name' => 'Jones'],
            ['first_name' => 'Charlotte','last_name' => 'Garcia'],
            ['first_name' => 'Benjamin', 'last_name' => 'Miller'],
            ['first_name' => 'Amelia',   'last_name' => 'Davis'],
            ['first_name' => 'Lucas',    'last_name' => 'Martinez'],
            ['first_name' => 'Sophia',   'last_name' => 'Rodriguez'],
            ['first_name' => 'Ethan',    'last_name' => 'Wilson'],
            ['first_name' => 'Mia',      'last_name' => 'Anderson'],


            ['first_name' => 'Sarah',    'last_name' => 'Morin'],
            ['first_name' => 'Gabriel',  'last_name' => 'Lamoureux'],
            ['first_name' => 'Victoria', 'last_name' => 'Desjardins'],
            ['first_name' => 'Samuel',   'last_name' => 'Charbonneau'],
            ['first_name' => 'Madison',  'last_name' => 'Dubois'],
            ['first_name' => 'Logan',    'last_name' => 'Bélanger'],
            ['first_name' => 'Jacob',    'last_name' => 'Cloutier'],
            ['first_name' => 'Ella',     'last_name' => 'Girard'],
            ['first_name' => 'Aiden',    'last_name' => 'Lemieux'],
            ['first_name' => 'Hannah',   'last_name' => 'Carrier'],
            ['first_name' => 'Caleb',    'last_name' => 'Paquette'],
            ['first_name' => 'Leah',     'last_name' => 'Gauthier'],
            ['first_name' => 'Daniel',   'last_name' => 'Chevalier'],
            ['first_name' => 'Sophie',   'last_name' => 'Mercier'],
            ['first_name' => 'Ryan',     'last_name' => 'Blais'],
        ];

        $speakrNames = [
            
            ['first_name' => 'James',    'last_name' => 'Taylor'],
            ['first_name' => 'Isabella', 'last_name' => 'Thomas'],
            ['first_name' => 'Logan',    'last_name' => 'Moore'],
            ['first_name' => 'Avery',    'last_name' => 'Martin'],
            ['first_name' => 'Jack',     'last_name' => 'Lavoie'],
            ['first_name' => 'Chloe',    'last_name' => 'Tremblay'],
            ['first_name' => 'Henry',    'last_name' => 'Roy'],
            ['first_name' => 'Émilie',   'last_name' => 'Gagnon'],
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
            ['first_name' => 'Aiden',    'last_name' => 'Lemieux'],
            ['first_name' => 'Hannah',   'last_name' => 'Carrier'],
            ['first_name' => 'Caleb',    'last_name' => 'Paquette'],
            ['first_name' => 'Leah',     'last_name' => 'Gauthier'],
            ['first_name' => 'Daniel',   'last_name' => 'Chevalier'],
            ['first_name' => 'Sophie',   'last_name' => 'Mercier'],
            ['first_name' => 'Ryan',     'last_name' => 'Blais'],
        ];

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
        $exhibitorAdmin->assignRole('Exhibitor');
        qrCode($exhibitorAdmin->id);


        $sponsors = User::create([
            'name' => "Sponsors$i",
            'lastname'=>'Admin',
            'email' => "sponsors$i@admin.com",
            'mobile' => '12345678',
            'tags'=>'Sponsors,admin,event',
            'designation'=>'Event Manager',
            'bio'=>"This is a short bio for Sponsors $i. Experienced in event management and exhibitions.",
            'password' => Hash::make('password')
        ]);
         $sponsors->assignRole('Sponsors');
         qrCode($sponsors->id);

        $speaker = User::create([
            'name' => "Speaker$i",
            'lastname'=>'Speaker',
            'email' => "speaker$i@speaker.com",
            'mobile' => '12345678',
            'password' => Hash::make('password'),
        ]);
        $speaker->assignRole('Speaker');
        qrCode($speaker->id);


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
                'tags'=>'Attendee',
                'designation'=>'Software Engineer',
                'bio'=>"This is a short bio for Attaindee $i. Experienced in event management and exhibitions.",
            ]);
            $attendee->assignRole('Attendee');
            qrCode($attendee->id);
            $exhibitorAdmin->company_id = $company->id;
            $exhibitorAdmin->save();
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
