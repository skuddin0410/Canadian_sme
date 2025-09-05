<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Blog;
use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;

class CategoryController extends Controller
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
       $categories = Category::orderBy(DB::raw("ISNULL(categories.order), categories.order"), 'ASC')->orderBy('created_at','DESC');
        if ($request->has('type')) {
        $query->where('type', $request->type);
    }

        if($request->search){
            $categories = $categories->where(function($query) use($request){
                    $query->where('name', 'LIKE', '%'. $request->search .'%');
                });
        }
       $categoriesCount = clone $categories;
        $totalRecords = $categoriesCount->count(DB::raw('DISTINCT(categories.id)'));  
        $categories = $categories->offset($offset)->limit($perPage)->get();       
        $categories = new LengthAwarePaginator($categories, $totalRecords, $perPage, $pageNo, [
                  'path'  => $request->url(),
                  'query' => $request->query(),
                ]);
        $data['offset'] = $offset;
        $data['pageNo'] = $pageNo;
        $categories->setPath(route('categories.index'));
        $data['html'] = view('category.table', compact('categories', 'perPage'))
                  ->with('i', $pageNo * $perPage)
                  ->render();

         return response($data);                                              
        }     
        return view('category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'type'=>'required|string'
        ]);

        if($validator->fails()){
            return redirect(route('categories.create'))->withInput()->withErrors($validator);
        }

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name, '-');
        $category->type = $request->type;
        $category->color = $request->color ?? '';
        $category->save();
        return redirect(route('categories.index'))->withSuccess("Category has been saved successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('category.create',["category" => $category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,'.$category->id,
            'type'=>'required|string',
        ]);

        if($validator->fails()){
            return redirect(route('categories.edit',["category"=>$category->id]))->withInput()->withErrors($validator);
        }

        $category->name = $request->name;
        $category->slug = Str::slug($request->name, '-');
        $category->type = $request->type;
        $category->color = $request->color ?? '';
        $category->save();
        return redirect(route('categories.index'))->withSuccess("Category has been saved successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {   
        if(Blog::where('category',$category->id)->first()){
          return redirect(route('categories.index'))->withError("Category can not be delete");   
        }
        $category->delete();
        return redirect(route('categories.index'))->withSuccess("Category has been deleted successfully");
    }

    public function order(Request $request){
        
       $category = Category::where('id',$request->id)->first();
       $category->order = $request->order == 0 ? null : $request->order;
       $category->save();
       return 'success';
    }
    

    public function storeTags(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->type = 'event';
        $category->save();
        
        return response()->json([
          'data'=>Category::where('type','tags')->get()
        ]);
    }

}
