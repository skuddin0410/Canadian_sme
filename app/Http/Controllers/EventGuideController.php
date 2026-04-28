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
use App\Models\User;
use App\Models\GeneralNotification;
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
        $eventIds = getEventIds();
        $galleryItems = GalleryItem::with(['user', 'event'])
            ->whereIn('event_id', $eventIds)
            ->where(function($query) {
                $query->where('is_approved', true)
                      ->orWhere('added_by', Auth::id());
            })
            ->latest()
            ->get();
    }

    $events = isSuperAdmin()
        ? Event::orderBy('title')->get()
        : Event::whereIn('id', getEventIds())->orderBy('title')->get();

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

    // Notify the uploader
    GeneralNotification::create([
        'user_id' => $item->added_by,
        'title' => 'Gallery Item Approved',
        'body' => "Your gallery item \"{$item->file_name}\" has been approved and is now visible.",
        'related_type' => 'gallery_item_approved',
        'is_read' => 0
    ]);

    return redirect()->route('event-guides.showGallery')->with('success', 'File approved successfully.');
}

public function approveAllGalleryItems(Request $request)
{
    if (!isSuperAdmin()) {
        return back()->with('error', 'Unauthorized action.');
    }

    $pendingItems = GalleryItem::where('is_approved', false)->get();
    $uploaders = $pendingItems->pluck('added_by')->unique();

    GalleryItem::where('is_approved', false)->update(['is_approved' => true]);

    foreach ($uploaders as $userId) {
        GeneralNotification::create([
            'user_id' => $userId,
            'title' => 'Gallery Items Approved',
            'body' => "All your pending gallery items have been approved.",
            'related_type' => 'gallery_items_approved',
            'is_read' => 0
        ]);
    }

    return redirect()->route('event-guides.showGallery')->with('success', 'All pending files approved successfully.');
}

    public function uploadGallery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'images.*' => 'required|file|mimes:jpg,jpeg,png,gif,pdf,mp4,mov,avi,wmv|max:10240',
        ]);

        // Set custom attribute names (filenames) before validation fails
        $attributes = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $key => $file) {
                // Safely get the original name if it's an uploaded file object
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $attributes["images.{$key}"] = $file->getClientOriginalName();
                }
            }
        }
        $validator->setAttributeNames($attributes);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

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

        if (!isSuperAdmin()) {
            $user = Auth::user();
            $event = Event::find($request->event_id);
            $superAdmin = User::find(1);
            if ($superAdmin) {
                GeneralNotification::create([
                    'user_id' => $superAdmin->id,
                    'title' => 'New Gallery Items Pending Approval',
                    'body' => "{$user->full_name} has uploaded new gallery items for event \"{$event->title}\" and is waiting for approval.",
                    'related_type' => 'gallery_approval_request',
                    'is_read' => 0
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully.'
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
    $uploaderId = $item->added_by;
    $fileName = $item->file_name;

    // Delete file from storage
    if (Storage::disk('public')->exists($item->file_path)) {
        Storage::disk('public')->delete($item->file_path);
    }

    $item->delete();

    // Notify the uploader if it was deleted by someone else (Super Admin)
    if (Auth::id() !== $uploaderId) {
        GeneralNotification::create([
            'user_id' => $uploaderId,
            'title' => 'Gallery Item Deleted',
            'body' => "Your gallery item \"{$fileName}\" has been deleted by the administrator.",
            'related_type' => 'gallery_item_deleted',
            'is_read' => 0
        ]);
    }

    return redirect()->route('event-guides.showGallery')->with('success', 'File deleted successfully.');
}






}
