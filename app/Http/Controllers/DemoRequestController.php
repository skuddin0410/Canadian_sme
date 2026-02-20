<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DemoRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemoStatusUpdatedMail;

class DemoRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = DemoRequests::latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $demos = $query->paginate(10);

        if ($request->ajax_request) {
            $html = view('demo.table', compact('demos'))->render();
            return response()->json(['html' => $html]);
        }

        return view('demo.index');
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirm,reschedule,cancel,completed',
            'note' => 'nullable|string|max:1000'
        ]);
         if (in_array($request->status, ['reschedule', 'cancel']) 
        && empty($request->note)) {
        
        return back()->withErrors([
            'note' => 'Note is required when rescheduling or cancelling.'
        ]);
    }

        $demo = DemoRequests::findOrFail($id);
        $demo->status = $request->status;
        $demo->note = $request->note;
        $demo->save();
         Mail::to($demo->email ?? optional($demo->user)->email)
        ->send(new DemoStatusUpdatedMail($demo));

        return back()->with('success', 'Status updated successfully.');
    }
}
