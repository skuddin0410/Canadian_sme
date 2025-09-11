<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Exports\LeadsExport;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{

public function index(Request $request)
{
    $query = Lead::with(['user']);

    // Search by lead's first_name or last_name
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%");
        });
    }


    // Optional filters: status, source
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('source')) {
        $query->where('source', $request->source);
    }

    $leads = $query->paginate(20)->withQueryString();

    return view('leads.index', compact('leads'));
}




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
          return view('leads.create');
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email|unique:leads,email',
            'phone'             => 'required|string|max:20',
            'status'            => 'nullable|in:new,contacted,qualified,converted,lost',
            'priority'          => 'nullable|in:low,medium,high',
            'source'            => 'nullable|in:website,referral,social_media,walk_in,phone,advertisement',
            'desired_amenities' => 'nullable|array',
            'tags'              => 'nullable|array',
        ]);

        Lead::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'status'            => $request->status ?? 'new',
            'priority'          => $request->priority ?? 'medium',
            'source'            => $request->source ?? 'website',
            'desired_amenities' => $request->desired_amenities ? json_encode($request->desired_amenities) : null,
            'tags'              => $request->tags ? json_encode($request->tags) : null,
        ]);


        return redirect()->route('leads.index')->with('success', 'Lead created successfully!');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        //
         $lead->load(['assignedAgent', 'matchedEvent', ]);
         return view('leads.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead)
    {
        //
        $selectedAmenities = $lead->desired_amenities 
        ? json_decode($lead->desired_amenities, true) 
        : [];
        $selectedTags = $lead->tags 
            ? json_decode($lead->tags, true) 
            : [];
            

    $amenities = ['parking','gym','pool','laundry','pet_friendly','balcony','concierge','storage']; // example list
    $tags = ['hot', 'warm', 'cold', 'follow-up'];

         return view('leads.edit', compact('lead','amenities', 'selectedAmenities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lead $lead)
    {
        //
         $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email|unique:leads,email,' . $lead->id,
            'phone'             => 'required|string|max:20',
            'status'            => 'nullable|in:new,contacted,qualified,converted,lost',
            'priority'          => 'nullable|in:low,medium,high',
            'source'            => 'nullable|in:website,referral,social_media,walk_in,phone,advertisement',
            'desired_amenities' => 'nullable|array',
            'tags'              => 'nullable|array',
        ]);

        $lead->update([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'status'            => $request->status,
            'priority'          => $request->priority,
            'source'            => $request->source,
            'desired_amenities' => $request->desired_amenities ? json_encode($request->desired_amenities) : null,
            'tags'              => $request->tags ? json_encode($request->tags) : null,
        ]);

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        //
        $lead->delete();
        
        return redirect()->route('leads.index')
            ->with('success', 'Lead deleted successfully!');
    }

public function export(Request $request, $format = 'xlsx')
{
    $fileName = 'leads_export_' . now()->format('Y_m_d_H_i_s');

    switch($format) {
        case 'csv':
            $fileName .= '.csv';
            $exportFormat = 'default';
            $writerType = \Maatwebsite\Excel\Excel::CSV;
            break;

        case 'xls':
            $fileName .= '.xls';
            $exportFormat = 'default';
            $writerType = \Maatwebsite\Excel\Excel::XLS;
            break;

        case 'crm': // CRM-Compatible format
            $fileName .= '_crm.csv'; 
            $exportFormat = 'crm';
            $writerType = \Maatwebsite\Excel\Excel::CSV;
            break;

        case 'xlsx':
        default:
            $fileName .= '.xlsx';
            $exportFormat = 'default';
            $writerType = \Maatwebsite\Excel\Excel::XLSX;
            break;
    }

    return Excel::download(new LeadsExport($exportFormat), $fileName, $writerType);
}

}
