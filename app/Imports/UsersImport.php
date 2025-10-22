<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Facades\Excel;

class UsersImport implements ToModel, WithStartRow
{
    protected $users = [];

    public function startRow(): int
    {  
        return 2; 
    }

    public function model(array $row)
    {   

        if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
            return null;
        }
        
    
        $existingUser = User::where('email', $row[2])->first();
        if ($existingUser) {
            return null; 
        }
        $this->users[] = [
            'name' => $row[0],
            'lastname' => $row[1],
            'email' => $row[2],
            'status' => $row[3] ?? '',
            'gdpr_consent' => ($row[4] == 'confirmed') ? 1 : 0,
            'bio' => $row[5] ?? '',
            'company' => $row[6] ?? '',
            'designation' => $row[7] ?? '',
            'mobile' => $row[8] ?? '',
            'dob' => $row[9] ?? '',
            'facebook_url' => $row[10] ?? '',
            'twitter_url' => $row[11] ?? '',
            'linkedin_url' => $row[12] ?? '',
            'instagram_url' => $row[13] ?? '',
            'slug' => createUniqueSlug('users', $row[0] . '_' . $row[1]),
            'primary_group' => 'Attendee',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Perform bulk insert after a certain number of records (e.g., 500)
        if (count($this->users) >= 500) {
            User::insert($this->users);
            $this->users = []; // Reset the users array after the insert
        }

        return null; // We don’t need to return anything for each row
    }

    public function __destruct()
    {
        // Insert any remaining users that weren’t inserted in the batch
        if (count($this->users) > 0) {
            User::insert($this->users);
        }
    }
}
