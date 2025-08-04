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
    protected $role;
    public function __construct($role)
    {
        $this->role = $role;
    }

    public function startRow(): int
    {  
        return 2; // Skip the header row
    }

    public function model(array $row)
    {   
        $import = ImportData::create([
            'first_name'=> $row[0] ?? '',
            'last_name'=> $row[1] ?? '',
            'email'=> $row[2] ?? '',
            'mobile'=> $row[3] ?? '',
            'added_by'=> auth()->user()->id
        ]);
        
        $contact = User::where('email', $row[2])->first();
        if( empty($contact) ){

            $username = strtolower($row[0]) . rand(10, 1000) . time();
            $user = new User();
            $user->name =  $row[0];
            $user->lastname = $row[1];
            $user->username = $username;
            $user->email = $row[2] ?? '';
            $user->mobile = $row[3] ?? '';
            $user->save();
            $user->assignRole($this->role);
        } 

    }
}
