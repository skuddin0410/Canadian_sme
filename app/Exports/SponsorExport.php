<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SponsorExport implements FromCollection , WithHeadings, WithMapping
{
    public function __construct(private readonly int $eventId)
    {
    }

   public function collection()
    {
        return Company::with(['user'])
            ->where('is_sponsor', 1)
            ->whereHas('eventAndEntityLinks', function ($query) {
                $query->where('event_id', $this->eventId);
            })
            ->orderBy("created_at", "DESC")
            ->get();
    }

    public function map($company): array
    {
        return [
            $company->id,
            $company->name,
            $company->email,
            $company->phone,
            $company->description,
            $company->website,
            $company->linkedin,
            $company->twitter,
            $company->facebook,
            $company->instagram,
           
            // optional($company->user)->email,
            // $company->boothUsers->pluck('booth.name')->implode(', '),
            $company->created_at->format('Y-m-d H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Company Name',
            'Email',
            'Phone',
          
             'description',
            'Website',
            'linkedin',
            'twitter',
            'facebook',
            'instagram',
            // 'Assigned Booths',
            'Created At',
        ];
    }
}
