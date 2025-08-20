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

class EventController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);

       if($request->ajax() && $request->ajax_request == true){
        $events = Event::with(['category','photo'])->orderBy('id','DESC');

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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:events,slug',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
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
            'image'=>'required|file|mimetypes:'.config('app.image_mime_types').'|max:'.config('app.user_image_size')
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['created_by'] = auth()->id(); // or any default user

        $event = Event::create($validated);
        $uploadPath = 'events';
        if($request->file("image")){
         $this->imageUpload($request->file("image"),$uploadPath,$event->id,'events','photo'); 
        }

        return redirect()->route('events.index')->with('success', 'Event created.');
    }

    public function show(Event $event)
    {
        return view('events.view', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {   
    
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:events,slug,' . $event->id,
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'youtube_link' => [
            'nullable',
            'url',
            'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[\w-]+$/'
        ],
            'status' => 'required|in:draft,published,cancelled',
            'visibility' => 'required|in:public,private,unlisted',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:1000',
            'tags'=>'nullable|string|max:1000',
            'image'=>'nullable|file|mimetypes:'.config('app.image_mime_types').'|max:'.config('app.banner_image_size')
        ]);

        $event->update($validated);
        $event->tags = $request->tags;
        $event->save();
        $uploadPath = 'events';
        if($request->file("image")){
          $this->imageUpload($request->file("image"),$uploadPath,$event->id,'events','photo',$idForUpdate=$event->id);   
        }

        return redirect()->route('events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }
}
