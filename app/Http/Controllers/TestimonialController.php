<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use DB;

class TestimonialController extends Controller
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
            $testimonials = $testimonials = Testimonial::orderBy(DB::raw("ISNULL(testimonials.order), testimonials.order"), 'ASC')->orderBy('created_at','DESC');
            $testimonialsCount = clone $testimonials;
            $totalRecords = $testimonialsCount->count(DB::raw('DISTINCT(testimonials.id)'));  
            $testimonials = $testimonials->offset($offset)->limit($perPage)->get();       
            $testimonials = new LengthAwarePaginator($testimonials, $totalRecords, $perPage, $pageNo, [
                      'path'  => $request->url(),
                      'query' => $request->query(),
                    ]);
            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $testimonials->setPath(route('testimonials.index'));
            $data['html'] = view('testimonial.table', compact('testimonials', 'perPage'))
                      ->with('i', $pageNo * $perPage)
                      ->render();

            return response($data);

        }
        return view('testimonial.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       return view('testimonial.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description'=>'required|string',
            'rating' => 'required|string'
        ]);

        if($validator->fails()){
            return redirect(route('testimonials.create'))->withInput()->withErrors($validator);
        }

        $testimonial = new Testimonial();
        $testimonial->name = $request->name;
        $testimonial->rating = $request->rating; 
        $testimonial->status = $request->status;
        $testimonial->message = strip_tags($request->description,'<a>');
        $testimonial->save();
        if(!empty($request->file("image"))){
        $this->imageUpload($request->file("image"),"testimonials",$testimonial->id,'testimonials','photo'); 
        }
        return redirect(route('testimonials.index'))->withSuccess("Testimonial has been saved successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimonial $testimonial)
    {
        return view('testimonial.view',["testimonial"=>$testimonial]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimonial $testimonial)
    {
        return view('testimonial.create',["testimonial" => $testimonial]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description'=>'required|string',
            'rating' => 'required|string'
        ]);

        if($validator->fails()){
            return redirect(route("testimonials.edit",["testimonial"=> $testimonial]))->withInput()->withErrors($validator);
        }

        $testimonial->name = $request->name;
        $testimonial->rating = $request->rating; 
        $testimonial->status = $request->status; 
        $testimonial->message = strip_tags($request->description,'<a>');
        $testimonial->save();
        if(!empty($request->file("image"))){
          $this->imageUpload($request->file("image"),"testimonials",$testimonial->id,'testimonials','photo',$idForUpdate=$testimonial->id); 
        }
        return redirect(route('testimonials.index'))->withSuccess("Testimonial data has been saved successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial)
    {   
        $this->deleteFile($testimonial->id,'testimonials');
        $testimonial->delete();
        return redirect(route('testimonials.index'))->withSuccess("Testimonial deleted successfully");
    }


    public function order(Request $request){
       $testimonial = Testimonial::where('id',$request->id)->first();
       if($testimonial->order !=  $request->order){
       $testimonial->order = $request->order == 0 ? null : $request->order;
       $testimonial->save();
       $this->orderSet($testimonial->id,$table="testimonials",$request->order);
       return 'success';
       }
    }
}
