<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;

class BannerController extends Controller
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
       $banners = Banner::with('photo')->orderBy(DB::raw("ISNULL(banners.order), banners.order"), 'ASC')->orderBy('created_at','DESC');
       $bannersCount = clone $banners;
        $totalRecords = $bannersCount->count(DB::raw('DISTINCT(banners.id)'));  
        $banners = $banners->offset($offset)->limit($perPage)->get();       
        $banners = new LengthAwarePaginator($banners, $totalRecords, $perPage, $pageNo, [
                  'path'  => $request->url(),
                  'query' => $request->query(),
                ]);
        $data['offset'] = $offset;
        $data['pageNo'] = $pageNo;
        $banners->setPath(route('banners.index'));
        $data['html'] = view('banner.table', compact('banners', 'perPage'))
                  ->with('i', $pageNo * $perPage)
                  ->render();

       return response($data);                                              
      }     
      return view('banner.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('banner.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'link' => 'required|string|max:255',
            'image'=>'required|file|mimetypes:'.config('app.image_mime_types').'|max:'.config('app.user_image_size')

        ]);
        if($validator->fails()){
            return redirect(route('banners.create'))->withInput()
                            ->withErrors($validator);
        }
        
        $banner = new Banner();
        $banner->name = $request->name;
        $banner->description = strip_tags($request->description,'<a>');
        $banner->link = $request->link;
        //$banner->order = $request->order;
        $banner->save();

        $this->imageUpload($request->file("image"),"banners",$banner->id,'banners','photo');
        return redirect(route('banners.index'))
                             ->withSuccess('Banner data has been saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        return view('banner.view',['banner'=>$banner]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        return view('banner.create',['banner'=>$banner]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {   
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'link' => 'required|string|max:255',
            'image'=>'nullable|file|mimetypes:'.config('app.image_mime_types').'|max:'.config('app.banner_image_size')

        ]);
        if($validator->fails()){
            return redirect(route('banners.edit',["banner"=>$banner->id]))->withInput()
                            ->withErrors($validator);
        }

        $banner->name = $request->name;
        $banner->description = strip_tags($request->description,'<a>');
        $banner->link = $request->link;
        //$banner->order = $request->order;
        $banner->save();
        
        if(!empty($request->file("image"))){
           $this->imageUpload($request->file("image"),"banners",$banner->id,'banners','photo',$idForUpdate=$banner->id);

        }
        return redirect(route('banners.index'))
                             ->withSuccess('Banner data has been saved successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {   
        $this->deleteFile($banner->id,'banners');
        $banner->delete();
        return redirect(route('banners.index'))
                             ->withSuccess('Banner deleted successfully');
    }

    public function order(Request $request){
       $banner = Banner::where('id',$request->id)->first();
       if($banner->order !=  $request->order){
       $banner->order = $request->order == 0 ? null : $request->order;
       $banner->save();
       $this->orderSet($banner->id,$table="banners",$request->order);
       return 'success';
       }
    }
}
