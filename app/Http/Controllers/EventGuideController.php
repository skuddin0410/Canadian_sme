<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventGuide;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Drive;
use App\Models\GalleryItem;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;


class EventGuideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);

        if ($request->ajax() && $request->ajax_request == true) {
            $guides = EventGuide::orderBy('id', 'DESC');

            // Search filter
            if ($request->search) {
                $guides = $guides->where(function ($query) use ($request) {
                    $query->where('title', 'LIKE', '%' . $request->search . '%');
                });
            }

            // Category filter
         

            // Clone query for count
            $guidesCount = clone $guides;
            $totalRecords = $guidesCount->count(DB::raw('DISTINCT(event_guides.id)'));

            // Pagination
            $guides = $guides->offset($offset)->limit($perPage)->get();

            $guides = new LengthAwarePaginator($guides, $totalRecords, $perPage, $pageNo, [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]);

            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $guides->setPath(route('event-guides.index'));

            // Render partial view for AJAX
            $data['html'] = view('event_guide.table', compact('guides', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
        }

        return view('event_guide.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('event_guide.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   



public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        // 'category' => 'required|string|max:255',
        'title'    => 'required|string|max:255',
        'type'     => 'required|string|max:100',
        'weblink'  => 'nullable|url|max:255',
        'doc'      => 'nullable|file|mimes:jpg,png,pdf,doc,docx|max:2048',
    ]);

    if ($validator->fails()) {
        return redirect()->route('event-guides.create')
            ->withInput()
            ->withErrors($validator);
    }

  
    $eventGuide = new EventGuide();
    // $eventGuide->category = $request->category;
    $eventGuide->title    = $request->title;
    $eventGuide->type     = $request->type;
    $eventGuide->weblink  = $request->weblink;
    $eventGuide->save();

 
    if ($request->hasFile('doc')) {
      

        
        $path = $this->imageUpload(
            $request->file('doc'),
            'event_guides',
            $eventGuide->id,
            'event_guides',
            'doc',
            $eventGuide->id
        );

        
        if (!empty($path)) {
            $eventGuide->doc = $path;
        }
    

         $eventGuide->save();
    }

    return redirect()->route('event-guides.index')
        ->withSuccess('Event Guide has been created successfully');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         $eventGuide = EventGuide::findOrFail($id);

        return view('event_guide.view', compact('eventGuide'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
         $eventGuide = EventGuide::findOrFail($id);

        return view('event_guide.edit', compact('eventGuide'));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, string $id)
{
    $validator = Validator::make($request->all(), [
        // 'category' => 'required|string|max:255',
        'title'    => 'required|string|max:255',
        'type'     => 'required|string|max:100',
        'weblink'  => 'nullable|url|max:255',
        'doc'      => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
    ]);

    if ($validator->fails()) {
        return redirect()->route('event-guides.edit', $id)
            ->withInput()
            ->withErrors($validator);
    }

    $eventGuide = EventGuide::findOrFail($id);

    // $eventGuide->category = $request->category;
    $eventGuide->title    = $request->title;
    $eventGuide->type     = $request->type;
    $eventGuide->weblink  = $request->weblink;
    
    // If a new file is uploaded, handle it using your existing imageUpload()
    if ($request->hasFile('doc')) {
      

        
        $path = $this->imageUpload(
            $request->file('doc'),
            'event_guides',
            $eventGuide->id,
            'event_guides',
            'doc',
            $eventGuide->id
        );

        
        if (!empty($path)) {
            $eventGuide->doc = $path;
        }
    }

    $eventGuide->save();

    return redirect()->route('event-guides.index')
        ->withSuccess('Event Guide has been updated successfully');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         EventGuide::findOrFail($id)->delete();
        return back()->with('success', 'EventGuide deleted successfully.');
        
    }

public function showGallery()
{
    // Superadmin sees everything
    if (isSuperAdmin()) {
        $galleryItems = GalleryItem::with(['user', 'event'])->latest()->get();
    } else {
        // Others see only approved items OR their own pending items
        $galleryItems = GalleryItem::with(['user', 'event'])
            ->where(function($query) {
                $query->where('is_approved', true)
                      ->orWhere('added_by', Auth::id());
            })
            ->latest()
            ->get();
    }

    $events = Event::orderBy('title')->get();

    return view('event_guide.gallery', compact('galleryItems', 'events'));
}

public function approveGalleryItem(Request $request)
{
    if (!isSuperAdmin()) {
        return back()->with('error', 'Unauthorized action.');
    }

    $request->validate([
        'id' => 'required|exists:gallery_items,id',
    ]);

    $item = GalleryItem::findOrFail($request->id);
    $item->update(['is_approved' => true]);

    return redirect()->route('event-guides.showGallery')->with('success', 'File approved successfully.');
}

public function approveAllGalleryItems(Request $request)
{
    if (!isSuperAdmin()) {
        return back()->with('error', 'Unauthorized action.');
    }

    GalleryItem::where('is_approved', false)->update(['is_approved' => true]);

    return redirect()->route('event-guides.showGallery')->with('success', 'All pending files approved successfully.');
}

public function uploadGallery(Request $request)
{
    $request->validate([
        'event_id' => 'required|exists:events,id',
        'images.*' => 'required|file|mimes:jpg,jpeg,png,gif,pdf,mp4,mov,avi,wmv|max:10240',
    ]);

    foreach ($request->file('images') as $file) {
        $path = $file->store('event_guides', 'public');
        $extension = strtolower($file->getClientOriginalExtension());
        
        $type = 'document';
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $type = 'image';
        } elseif (in_array($extension, ['mp4', 'mov', 'avi', 'wmv'])) {
            $type = 'video';
        }

        GalleryItem::create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $type,
            'added_by' => Auth::id(),
            'is_approved' => isSuperAdmin() ? true : false,
            'event_id' => $request->event_id,
        ]);
    }

    return redirect()->route('event-guides.showGallery')->with('success', 'Files uploaded successfully.');
}

public function deleteGalleryImage(Request $request)
{
    $request->validate([
        'id' => 'required|exists:gallery_items,id',
    ]);

    $item = GalleryItem::findOrFail($request->id);

    // Delete file from storage
    if (Storage::disk('public')->exists($item->file_path)) {
        Storage::disk('public')->delete($item->file_path);
    }

    $item->delete();

    return redirect()->route('event-guides.showGallery')->with('success', 'File deleted successfully.');
}






}
