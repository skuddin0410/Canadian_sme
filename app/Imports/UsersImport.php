<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\ImportData;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class UsersImport implements ToModel,WithStartRow
{   

    public function startRow(): int
    {  
        return 2; // Skip the header row
    }

    public function model(array $row)
    {   
        $import = ImportData::create([
             "name" =>  $row[0],
            "lastname" => $row[1],
            "email" => $row[2] ?? '',
            "status" => $row[3] ?? '',
            "gdpr_consent" => $row[4] == 'confirmed' ? 1 : 0,
            "bio" => $row[5] ?? '',
            "company" => $row[6] ?? '',
            "designation" => $row[7] ?? '',
            "mobile" => $row[8] ?? '',
            "dob" => $row[9] ?? '',
            "facebook_url" => $row[10] ?? '',
            "twitter_url" => $row[11] ?? '',
            "linkedin_url" => $row[12] ?? '',
            "instagram_url" => $row[13] ?? '',
        ]);
        
        $contact = User::where('email', $row[2])->first();
        if( empty($contact) ){
            $user = new User();
            $user->name =  $row[0];
            $user->lastname = $row[1];
            $user->email = $row[2] ?? '';
            $user->status =  $row[3] ?? '';
            $user->gdpr_consent =  ($row[4] == 'confirmed') ? 1 : 0;
            $user->bio = $row[5] ?? '';
            $user->company = $row[6] ?? '';
            $user->designation = $row[7] ?? '';
            $user->mobile = $row[8] ?? '';
            $user->dob = $row[9] ?? '';
            $user->facebook_url = $row[10] ?? '';
            $user->twitter_url = $row[11] ?? '';
            $user->linkedin_url = $row[12] ?? '';
            $user->instagram_url = $row[13] ?? '';
            $user->save();
            $user->assignRole('Attendee');

            qrCode($user->id);
            //notification($user->id);
            //sendNotification("Welcome Email",$user);
        } 

    }
}
