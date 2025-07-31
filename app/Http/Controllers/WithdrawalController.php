<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use DB;

class WithdrawalController extends Controller
{
    public function index(Request $request){

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

    public function show(Request $request){
       $withdrawal = Withdrawal::with('user')->where('id',$request->request_id)->first(); 
       return view('withdrawal.view',compact('withdrawal'));
    }

     public function approveReject(Request $request){
        $withdrawal = Withdrawal::where("id",$request->id)->first();
        if($request->status == 'success' && str_replace(' ', '', $request->reference)  == ''){
            return redirect(route("withdrawals-requests",["request_id"=> $withdrawal->id ]))
                             ->withError("Please enter reference id");
        }
        $withdrawal->status = $request->status;
        $withdrawal->reference = $request->reference ?? null;
        $withdrawal->save();
        $msg = "Withdrawal has been approved successfully";
        if($request->status == 'failed'){
            $msg = "Withdrawal has been rejected successfully";
        }
        return redirect(route("withdrawals-requests",["request_id"=> $withdrawal->id ]))
                             ->withSuccess($msg);
    }
}
