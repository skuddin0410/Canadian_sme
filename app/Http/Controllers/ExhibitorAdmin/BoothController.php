<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booth;
use App\Models\Company;

use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;

class BoothController extends Controller
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
        $booths = Booth::with('company')->orderBy('id','DESC');

        if($request->search){
            $booths = $booths->where(function($query) use($request){
                    $query->where('name', 'LIKE', '%'. $request->search .'%');
                });
        }


        $boothsCount = clone $booths;
        $totalRecords = $boothsCount->count(DB::raw('DISTINCT(booths.id)'));  
        $booths = $booths->offset($offset)->limit($perPage)->get();       
        $booths = new LengthAwarePaginator($booths, $totalRecords, $perPage, $pageNo, [
                  'path'  => $request->url(),
                  'query' => $request->query(),
                ]);
        $data['offset'] = $offset;
        $data['pageNo'] = $pageNo;
        $booths->setPath(route('booths.index'));
        $data['html'] = view('company.booths.table', compact('booths', 'perPage'))
                  ->with('i', $pageNo * $perPage)
                  ->render();

         return response($data);                                              
        }   
                   
        return view('company.booths.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        return view('company.booths.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
         $request->validate([
            'title' => 'required|string|max:255',
            'booth_number' => 'required|string|max:50',
            'size' => 'required|string|max:50',
            'location_preferences' => 'required|string',
        ]);

        Booth::create($request->all());

        return redirect()->route('booths.index')->with('success', 'Booth created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    $booth = Booth::with('company')->findOrFail($id);
    return view('company.booths.show', compact('booth'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $booth = Booth::findOrFail($id);
        $companies = Company::all();
        return view('company.booths.edit', compact('booth', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $booth = Booth::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'booth_number' => 'required|string|max:50',
            'size' => 'required|string|max:50',
            'location_preferences' => 'required|string',
        ]);

        $booth->update($request->all());

        return redirect()->route('booths.index')->with('success', 'Booth updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
         Booth::findOrFail($id)->delete();
        return back()->with('success', 'Booth deleted successfully.');
    }
}
