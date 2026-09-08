<?php

namespace App\Exports;

use App\Models\Speaker;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SpeakersExport implements FromCollection,  WithHeadings, WithMapping
{
    public function __construct(private readonly int $eventId)
    {
    }

    public function collection()
    {
        return Speaker::query()
            ->whereHas('eventAndEntityLinks', function ($query) {
                $query->where('event_id', $this->eventId);
            })
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name . ' ' . $user->lastname,
            $user->email,
            $user->mobile,
            $user->company,
            $user->designation,
            $user->website_url,
            $user->linkedin_url,
            $user->instagram_url,
            $user->facebook_url ,
            $user->twitter_url,
            $user->bio,

            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Mobile',
            'Company',
            'Designation',
            'Website',
            'Linkedin',
            'Instagram',
            'Facebook',
            'Twitter',
            'Bio',
            'Registered At',
        ];
    }
}
