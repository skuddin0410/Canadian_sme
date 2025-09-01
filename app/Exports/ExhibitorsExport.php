<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExhibitorsExport implements FromCollection , WithHeadings, WithMapping
{
    public function collection()
    {
        // Only non-sponsors (based on your index method)
        return Company::with(['user', 'boothUsers.booth'])
            ->where('is_sponsor', 0)
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
            $company->website,
            optional($company->user)->email,
            $company->boothUsers->pluck('booth.name')->implode(', '),
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
            'Website',
            'User Email',
            'Assigned Booths',
            'Created At',
        ];
    }
}
