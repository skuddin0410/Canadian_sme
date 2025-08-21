<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
         $user = User::select('name','lastname',"email","mobile","dob","gender","place","street","zipcode","city","state","country")
        ->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Admin",'Admin','Representative','Attendee','Speaker','Support Staff Or Helpdesk','Registration Desk']);
        })->get()->makeHidden(['full_name']);

        return $user;
    }

    public function headings(): array
    {
        return ["First Name","Last Name","Email","Mobile","DOB","Gender","Place","Street","Zipcode","City","State","Country"];
    }
}
