<?php

namespace App\Http\Controllers;

use App\Mail\EventSupportStatus;
use Illuminate\Http\Request;
use App\Models\Support;
use App\Models\EventSupport;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportStatusUpdated;

class ContactUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 20);

        $query = EventSupport::with('event')
            ->orderBy('id', 'DESC');

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $contactUs = $query->paginate($perPage);

        if ($request->ajax() && $request->ajax_request == true) {

            $data['html'] = view('contact-us.table', compact('contactUs'))
                ->render();

            return response()->json($data);
        }

        return view('contact-us.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $support = Eventsupport::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,inprogress,completed'
        ]);

        $support->update([
            'status' => $request->status
        ]);
        Mail::to($support->email)->send(new EventSupportStatus($support));

        return back()->with('success', 'Status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
