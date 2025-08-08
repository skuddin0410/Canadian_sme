<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use App\Models\Category;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    { 
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);

      if($request->ajax() && $request->ajax_request == true){
        $pages = Page::with(['category','photo'])->orderBy('id','DESC');

        if($request->search){
            $pages = $pages->where(function($query) use($request){
                    $query->where('name', 'LIKE', '%'. $request->search .'%');
                });
        }

        if($request->category){
            $pages = $pages->where(function($query) use($request){
                $query->where('category', $request->category);
            });
        }

        $pagesCount = clone $pages;
        $totalRecords = $pagesCount->count(DB::raw('DISTINCT(pages.id)'));  
        $pages = $pages->offset($offset)->limit($perPage)->get();       
        $pages = new LengthAwarePaginator($pages, $totalRecords, $perPage, $pageNo, [
                  'path'  => $request->url(),
                  'query' => $request->query(),
                ]);
        $data['offset'] = $offset;
        $data['pageNo'] = $pageNo;
        $pages->setPath(route('pages.index'));
        $data['html'] = view('pages.table', compact('pages', 'perPage'))
                  ->with('i', $pageNo * $perPage)
                  ->render();

         return response($data);                                              
        }
        $catgories = Category::orderBy(DB::raw("ISNULL(categories.order), categories.order"), 'ASC')
                   ->orderBy('created_at','DESC')->get();   
                   
        return view('pages.index',["catgories"=>$catgories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {    
         $request->request->add(['slug' => createSlug($request->slug)]);
         $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'image'=>'required|file|mimetypes:'.config('app.image_mime_types').'|max:'.config('app.page_image_size'),
            'tags' => 'required',
            'description' => 'required|string',
            'meta_title'=>"nullable|max:255",
            'meta_keywords'=>"nullable|max:255",
            'status' => 'required',
            'start_date' => 'required_unless:status,scheduled|date',
            'end_date' => 'required_unless:status,scheduled|date'
        ]);
        if($validator->fails()){
            return redirect(route('pages.create'))->withInput()
                            ->withErrors($validator);
        }

        $uploadPath = "pages";
        $page = new Page();
        $page->name = $request->title;
        $page->slug = createSlug($request->slug);  
        $page->category = $request->category;
        $page->tags = $request->tags; 
        $page->description = $request->description;
        $page->meta_title = $request->meta_title;
        $page->meta_description = $request->meta_description ? strip_tags($request->meta_description): null;
        $page->created_by = \Auth::user()->id;

        $page->meta_keywords = $request->meta_keywords;
        $page->status = $request->status;
        $page->end_date = $request->end_date;
        $page->start_date = $request->start_date;
        $page->save();
        if($request->file("image")){
         $this->imageUpload($request->file("image"),$uploadPath,$page->id,'pages','photo'); 
        }
        return redirect(route('pages.index'))
                             ->withSuccess('Page data has been saved successfully');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page)
    {   
        $page = Page::with(['category','photo'])->find($page->id);
        return view('pages.view',['page'=>$page]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return view('pages.edit',['page'=>$page]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {   
        $request->request->add(['slug' => createSlug($request->slug)]);
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,'.$page->id,
            'image'=>'nullable|file|mimetypes:'.config('app.image_mime_types').'|max:'.config('app.page_image_size'),
            'tags' => 'required',
            'description' => 'required|string',
            'meta_title'=>"nullable|max:255",
            'meta_keywords'=>"nullable|max:255",
            'status' => 'required',
            'start_date' => 'required_unless:status,scheduled|date',
            'end_date' => 'required_unless:status,scheduled|date'
        ]);

        if($validator->fails()){
            return redirect(route('pages.edit',["page"=>$page->id]))->withInput()
                            ->withErrors($validator);
        }

        $uploadPath = "pages";

        $page->name = $request->title; 
        $page->category = $request->category;
        $page->tags = $request->tags; 
        $page->description = $request->description;
        $page->meta_title = $request->meta_title;
        $page->meta_description = $request->meta_description ? strip_tags($request->meta_description): null;
        $page->meta_keywords = $request->meta_keywords;
        $page->created_by = \Auth::user()->id;
        $page->status = $request->status;
        $page->end_date = $request->end_date;
        $page->start_date = $request->start_date;
        $page->save();
        if($request->file("image")){
          $this->imageUpload($request->file("image"),$uploadPath,$page->id,'pages','photo',$idForUpdate=$page->id);   
        }
        
        return redirect(route('pages.index'))
                            ->withSuccess('Page data has been saved successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {   
        $this->deleteFile($page->id,'pages');
        $page->delete();
        return redirect(route('pages.index'))
                            ->withSuccess('Page deleted successfully');
    }
}
