<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Support;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
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
            $supports = Support::orderBy('id', 'DESC');

            
            if ($request->search) {
                $supports = $supports->where(function ($query) use ($request) {
                    $query->where('subject', 'LIKE', '%' . $request->search . '%')
                          ->orWhere('description', 'LIKE', '%' . $request->search . '%');
                });
            }

          
            $supportsCount = clone $supports;
            $totalRecords = $supportsCount->count(DB::raw('DISTINCT(supports.id)'));

          
            $supports = $supports->offset($offset)->limit($perPage)->get();

            $supports = new LengthAwarePaginator($supports, $totalRecords, $perPage, $pageNo, [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]);

            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $supports->setPath(route('supports.index'));

           
            $data['html'] = view('support.table', compact('supports', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
        }

       
        return view('support.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
