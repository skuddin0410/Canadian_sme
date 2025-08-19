<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadsExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // return Lead::all();
         return Lead::all()->map(function($lead) {
            return [
                'First Name'        => $lead->first_name,
                'Last Name'         => $lead->last_name,
                'Email'             => $lead->email,
                'Phone'             => $lead->phone,
                'Status'            => $lead->status,
                'Priority'          => $lead->priority,
                'Source'            => $lead->source,
                'Tags'              => $lead->tags ? implode(', ', json_decode($lead->tags, true)) : '',
                'Desired Amenities' => $lead->desired_amenities ? implode(', ', json_decode($lead->desired_amenities, true)) : '',
                'Created At'        => $lead->created_at,
            ];
        });
    }
     public function headings(): array
    {
        return [
            'First Name', 'Last Name', 'Email', 'Phone', 
            'Status', 'Priority', 'Source', 'Tags', 'Desired Amenities', 'Created At'
        ];
    }
}
