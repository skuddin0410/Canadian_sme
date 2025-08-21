<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadsExport implements FromCollection, WithHeadings
{
    protected $format;

    public function __construct($format = 'default')
    {
        $this->format = $format;
    }

    public function collection()
    {
        $leads = Lead::all();

        return $leads->map(function($lead) {
            if ($this->format === 'crm') {
                // CRM Compatible Format
                return [
                    'first_name'        => $lead->first_name,
                    'last_name'         => $lead->last_name,
                    'email_address'     => $lead->email,
                    'phone_number'      => $lead->phone,
                    'lead_status'       => $lead->status,
                    'lead_priority'     => $lead->priority,
                    'lead_source'       => $lead->source,
                    'lead_tags'         => $lead->tags ? implode('|', json_decode($lead->tags, true)) : '',
                    'desired_amenities' => $lead->desired_amenities ? implode('|', json_decode($lead->desired_amenities, true)) : '',
                    'created_at'        => $lead->created_at->toDateTimeString(),
                ];
            }

            // Default export format
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
        if ($this->format === 'crm') {
            return [
                'first_name', 'last_name', 'email_address', 'phone_number',
                'lead_status', 'lead_priority', 'lead_source',
                'lead_tags', 'desired_amenities', 'created_at'
            ];
        }

        return [
            'First Name', 'Last Name', 'Email', 'Phone',
            'Status', 'Priority', 'Source', 'Tags', 'Desired Amenities', 'Created At'
        ];
    }
}
