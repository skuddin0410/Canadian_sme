<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Drive;
use App\Models\EventAndEntityLink;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);

       if($request->ajax() && $request->ajax_request == true){
        $events = Event::with(['category','photo','mapImage'])->orderBy('id','DESC');

        $events = isSuperAdmin() ? $events : $events->where('created_by',auth()->id());
        
        if($request->search){
            $events = $events->where(function($query) use($request){
                    $query->where('name', 'LIKE', '%'. $request->search .'%');
                });
        }

        if($request->category){
            $events = $events->where(function($query) use($request){
                $query->where('category', $request->category);
            });
        }

        $eventsCount = clone $events;
        $totalRecords = $eventsCount->count(DB::raw('DISTINCT(events.id)'));  
        $events = $events->offset($offset)->limit($perPage)->get();       
        $events = new LengthAwarePaginator($events, $totalRecords, $perPage, $pageNo, [
                  'path'  => $request->url(),
                  'query' => $request->query(),
                ]);
        $data['offset'] = $offset;
        $data['pageNo'] = $pageNo;
        $events->setPath(route('events.index'));
        $data['html'] = view('events.table', compact('events', 'perPage'))
                  ->with('i', $pageNo * $perPage)
                  ->render();

         return response($data);                                              
        }
        $catgories = Category::orderBy(DB::raw("ISNULL(categories.order), categories.order"), 'ASC')
                   ->orderBy('created_at','DESC')->get();   
                   
        return view('events.index',["catgories"=>$catgories]);
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:events,slug',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'about' => 'nullable|string',
            'terms_condition' => 'nullable|string',
            'help_support' => 'nullable|string',
            'privacy_policy' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:draft,published,cancelled',
            'youtube_link' => [
            'nullable',
            'url',
            'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[\w-]+$/'
        ],
            'visibility' => 'required|in:public,private,unlisted',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:1000',
            'tags'=>'nullable|string|max:1000',
            'image'=>'required|file|mimetypes:'.config('app.image_mime_types').'|max:'.config('app.user_image_size'),
            'map_image'=>'nullable|file|mimetypes:'.config('app.image_mime_types').'|max:'.config('app.user_image_size')
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['created_by'] = auth()->id(); // or any default user

        $event = Event::create($validated);
        $uploadPath = 'events';
        if($request->file("image")){
         $this->imageUpload($request->file("image"),$uploadPath,$event->id,'events','photo'); 
        }

        if($request->file("map_image")){
         $this->imageUpload($request->file("map_image"),$uploadPath,$event->id,'events','map_image'); 
        }

        return redirect()->route('events.index')->with('success', 'Event created.');
    }

    // public function clone($id)
    // {
    //     // Find the event by ID
    //     $event = Event::find($id);

    //     if (!$event) {
    //         return response()->json(['success' => false, 'message' => 'Event not found'], 404);
    //     }

    //     // Clone the event by copying its properties
    //     $clonedEvent = $event->replicate(); // Replicates the event but doesn't save it yet

    //     // Modify the title to include 'Clone' and ensure unique slug
    //     $clonedEvent->title = $event->title . ' Clone';
    //     $clonedEvent->slug = Str::slug($clonedEvent->title);
    //     $clonedEvent->created_by = auth()->id(); // The current logged-in user will be the creator of the cloned event

    //     // Save the cloned event
    //     $clonedEvent->save();

    //     // Handle image upload if necessary (you can add this logic based on your `store` function)
    //     if ($event->image) {
    //         $this->imageUpload($event->image, 'events', $clonedEvent->id, 'events', 'photo');
    //     }

    //     // Return success response
    //     return response()->json(['success' => true, 'message' => 'Event cloned successfully.']);
    // }


    public function clone($eventId)
    {
        // Get the event that you want to clone
        $event = Event::findOrFail($eventId);

        // Clone the event
        $clonedEvent = $event->replicate();
        
        // Modify the title to indicate it's a clone
        $clonedEvent->title = $event->title . ' Clone';

        // Generate the slug based on the new title
        $slug = Str::slug($clonedEvent->title);

        // First check: Check if the slug already exists
        $slugCount = Event::where('slug', $slug)->count();

        // If the slug exists, append a number and check again
        $attempt = 1;
        while ($slugCount > 0) {
            $slug = Str::slug($clonedEvent->title . '-' . $attempt); // Modify the slug
            $slugCount = Event::where('slug', $slug)->count(); // Re-check if the new slug exists
            $attempt++;
        }

        // Set the unique slug
        $clonedEvent->slug = $slug;

        // Set other fields as necessary (e.g., created_by)
        $clonedEvent->created_by = auth()->id();

        // Save the cloned event
        $clonedEvent->save();

        if($event->photo){
         Drive::create([
            'table_id' => $clonedEvent->id,
            'table_type' => 'events',
            'file_type' => 'photo',
            'file_name' => $event->photo->file_name,
            'is_local_file' => $event->photo->is_local_file,
         ]);
        }

        if($event->mapImage){
         Drive::create([
            'table_id' => $clonedEvent->id,
            'table_type' => 'events',
            'file_type' => 'map_image',
            'file_name' => $event->mapImage->file_name,
            'is_local_file' => $event->mapImage->is_local_file,
         ]);
        }

        // Clone the related records from `event_and_entity_link`
        $eventAndEntityLinks = EventAndEntityLink::where('event_id', $eventId)->get();
        // If no records are found, log a message
        if ($eventAndEntityLinks->isEmpty()) {
            \Log::warning('No event_and_entity_link records found for event ID: ' . $eventId);
        }

        foreach ($eventAndEntityLinks as $link) {
            // Log the link being cloned
            \Log::info('Cloning event_and_entity_link record with entity_type: ' . $link->entity_type . ' and entity_id: ' . $link->entity_id);

            $newLink = EventAndEntityLink::create([
                'event_id' => $clonedEvent->id, // Set the cloned event ID
                'entity_type' => $link->entity_type,
                'entity_id' => $link->entity_id,
            ]);

            // Log after creating the record
            if ($newLink) {
                \Log::info('Successfully cloned event_and_entity_link record for new event ID: ' . $clonedEvent->id);
            } else {
                \Log::error('Failed to clone event_and_entity_link record for event ID: ' . $clonedEvent->id);
            }
        }

        // return redirect()->route('events.index')->with('success', 'Event cloned successfully!');

        return response()->json(['success' => true, 'message' => 'Event cloned successfully.']);
    }

    public function show(Event $event)
    {
        return view('events.view', compact('event'));
    }

    public function edit(Event $event)
    {   
        $availableTags = Category::where('type','event')->pluck('name');
        return view('events.edit', compact('event','availableTags'));
    }

    public function update(Request $request, Event $event)
    {   
        // dd($request->all());
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:events,slug,' . $event->id,
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'about' => 'nullable|string',
            'privacy_policy' => 'nullable|string',
            'terms_condition' => 'nullable|string',
            'help_support' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'youtube_link' => [
            'nullable',
            'url',
            'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[\w-]+$/'
        ],
            'status' => 'required|in:draft,published,cancelled',
            'visibility' => 'required|in:listed,unlisted',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255', // each tag must be a string (optional but safer)
            'image'=>'nullable|file|mimetypes:'.config('app.image_mime_types').'|max:'.config('app.banner_image_size'),
            'map_image'=>'nullable|file|mimetypes:'.config('app.image_mime_types').'|max:'.config('app.banner_image_size')
        ]);

        $validated['tags'] = $request->has('tags') && !empty($request->tags) ? implode(',', $request->tags) : '';
        $event->title        = $validated['title'];
        $event->slug         = $validated['slug'] ?? $event->slug;
        $event->description  = $validated['description'] ?? null;
        $event->location     = $validated['location'] ?? null;
        $event->start_date   = $validated['start_date'];
        $event->end_date     = $validated['end_date'];
        $event->youtube_link = $validated['youtube_link'] ?? null;
        $event->status       = $validated['status'];
        $event->visibility   = $validated['visibility'];
        $event->tags         = $validated['tags'];
        $event->about        = $validated['about'] ?? null;
        $event->privacy_policy = $validated['privacy_policy'] ?? null;
        $event->terms_condition = $validated['terms_condition'] ?? null;
        $event->help_support = $validated['help_support'] ?? null;
        $event->save();
        if($request->file("image")){
          $this->imageUpload($request->file("image"),'events',$event->id,'events','photo',$idForUpdate=$event->id);   
        }

        if($request->file("map_image")){
          $this->imageUpload($request->file("map_image"),'events',$event->id,'events','map_image',$idForUpdate=$event->id);   
        }
     
        return redirect()->route('events.edit',['event'=>$event->id])->with('success', 'Event updated successfully');
    }

    public function destroy(Event $event)
    {   
        // Delete related records in event_and_entity_link first
        EventAndEntityLink::where('event_id', $event->id)->delete();
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }

    public function removePhoto(Request $request){
        $drive = Drive::where('id',$request->photo_id)->first();
        if ($drive) {
            $drive->delete();
        }
        return response()->json(['message' => 'Photo deleted successfully.']);
    }
}
