<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use DB;

class FaqController extends Controller
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
            $faqs = Faq::orderBy(DB::raw("ISNULL(faqs.order), faqs.order"), 'ASC')->orderBy('created_at','DESC');
            $faqsCount = clone $faqs;
            $totalRecords = $faqsCount->count(DB::raw('DISTINCT(faqs.id)'));  
            $faqs = $faqs->offset($offset)->limit($perPage)->get();       
            $faqs = new LengthAwarePaginator($faqs, $totalRecords, $perPage, $pageNo, [
                      'path'  => $request->url(),
                      'query' => $request->query(),
                    ]);
            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $faqs->setPath(route('faqs.index'));
            $data['html'] = view('faq.table', compact('faqs', 'perPage'))
                      ->with('i', $pageNo * $perPage)
                      ->render();

            return response($data);

        }
        return view('faq.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('faq.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description'=>'required|string'
        ]);

        if($validator->fails()){
            return redirect(route('faqs.create'))->withInput()->withErrors($validator);
        }

        $faq = new Faq();
        $faq->name = $request->name;
        $faq->description = strip_tags($request->description,'<a>');
        $faq->save();
        return redirect(route('faqs.index'))->withSuccess("Data has been saved successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Faq $faq)
    {
       return view('faq.view',["faq" => $faq]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faq $faq)
    {
        return view('faq.create',["faq" => $faq]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faq $faq)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description'=>'required|string'
        ]);

        if($validator->fails()){
            return redirect(route('faqs.edit', ["faq" => $faq->id] ) )->withErrors($validator);
        }

        $faq->name = $request->name;
        $faq->description = strip_tags($request->description,'<a>');
        $faq->save();
        return redirect(route('faqs.index'))->withInput()->withSuccess("Data has been saved successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect(route('faqs.index'))->withSuccess("Faq deleted successfully");
    }

     public function order(Request $request){
       $faq = Faq::where('id',$request->id)->first();
       if($faq->order !=  $request->order){
        $faq->order = $request->order == 0 ? null : $request->order;
        $faq->save();
        $this->orderSet($faq->id,$table="faqs",$request->order);
       }
       return 'success';
    }
}
