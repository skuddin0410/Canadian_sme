<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendeesExport implements FromCollection ,  WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }
  
    public function collection()
    {
        $isSuperAdmin = isSuperAdmin();
        
        $query = User::with('roles');

        // Logic exactly as in AttendeeUserController@index
        if ($isSuperAdmin) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Admin');
            });
        } else {
            $eventIds = getEventIds();
            $attendeeIds = DB::table('event_and_entity_link')
                ->where('entity_type', 'users')
                ->whereIn('event_id', $eventIds)
                ->pluck('entity_id')
                ->toArray();

            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Admin');
            })
            ->where(function($q) use ($attendeeIds) {
                $q->whereIn('users.id', $attendeeIds)
                ->orWhere('created_by', auth()->id());
            });
        }

        // Apply filters from request if available
        if ($this->request) {
            if ($this->request->filled('event_id')) {
                $eventId = $this->request->event_id;
                // Double check permission for this event_id
                if (!$isSuperAdmin && !in_array($eventId, getEventIds())) {
                    $eventId = 0;
                }

                if ($eventId !== 0) {
                    $filteredAttendeeIds = DB::table('event_and_entity_link')
                        ->where('event_id', $eventId)
                        ->where('entity_type', 'users')
                        ->pluck('entity_id')
                        ->toArray();
                    $query->whereIn('users.id', $filteredAttendeeIds);
                } else {
                    $query->whereRaw('1 = 0'); // Force empty result if unauthorized
                }
            }

            if ($this->request->filled('search')) {
                $search = $this->request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%')
                        ->orWhere('lastname', 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw("CONCAT(name, ' ', lastname)"), 'LIKE', "%{$search}%")
                        ->orWhere('designation', 'LIKE', '%' . $search . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $search . '%')
                        ->orWhere('company', 'LIKE', '%' . $search . '%');
                });
            }

            if ($this->request->filled('start_at') && $this->request->filled('end_at')) {
                $query->whereBetween('created_at', [$this->request->start_at, $this->request->end_at]);
            }

            if ($this->request->has('exhibitor_id')) {
                $query->where('created_by_exhibitor_id', $this->request->exhibitor_id);
            }

            if ($this->request->has('onsignal') && $this->request->onsignal == 1) {
                $query->whereNotNull('onesignal_userid');
            }
        }

        return $query->orderBy('created_at', 'DESC')->get();
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
