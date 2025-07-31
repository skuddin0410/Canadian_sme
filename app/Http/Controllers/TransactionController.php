<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        if($request->ajax() && $request->ajax_request == true){
            $withdrawal = Withdrawal::with('user')->orderBy('created_at','DESC');
            if($request->search){
                $withdrawal = $withdrawal->whereHas("user", function($query) use($request) {
                    $query->where('users.name', 'LIKE', '%'. $request->search .'%');
                    $query->orWhere('users.username', 'LIKE', '%'. $request->search .'%');
                    $query->orWhere('users.mobile', 'LIKE', '%'. $request->search .'%');
                    $query->orWhere('users.email', 'LIKE', '%'. $request->search .'%');
                });

            }
            if($request->status){
                $withdrawal = $withdrawal->where('withdrawals.status',$request->status);
            }

            $withdrawalCount = clone $withdrawal;
            $totalRecords = $withdrawalCount->count(DB::raw('DISTINCT(withdrawals.id)'));  
            $withdrawal = $withdrawal->offset($offset)->limit($perPage)->get();       
            $withdrawal = new LengthAwarePaginator($withdrawal, $totalRecords, $perPage, $pageNo, [
                      'path'  => $request->url(),
                      'query' => $request->query(),
                    ]);
            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $withdrawal->setPath(route('withdrawals-request'));
            $data['html'] = view('withdrawal.table', compact('withdrawal', 'perPage'))
                      ->with('i', $pageNo * $perPage)
                      ->render();

            return response($data);
        }
        return view('withdrawal.index');
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
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
