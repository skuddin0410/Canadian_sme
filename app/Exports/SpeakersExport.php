<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SpeakersExport implements FromCollection,  WithHeadings, WithMapping
{
    public function collection()
    {
        return User::with('roles')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Speaker');
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
            $user->tags ,
            $user->website_url,
            $user->linkedin_url,
            $user->instagram_url,
            $user->facebook_url ,
            $user->twitter_url,
            $user->mobile ,
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
            'Tags',
            'Website',
            'Linkedin',
            'Instagram',
            'Facebook',
            'Twitter',
            'Mobile',
            'Bio',
            'Registered At',
        ];
    }
}
