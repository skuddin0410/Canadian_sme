<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
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
            $order = Order::with('user')->orderBy('created_at','DESC');
            if($request->search){
                $order = $order->whereHas("user", function($query) use($request) {
                    $query->where('users.name', 'LIKE', '%'. $request->search .'%');
                    $query->orWhere('users.username', 'LIKE', '%'. $request->search .'%');
                    $query->orWhere('users.mobile', 'LIKE', '%'. $request->search .'%');
                    $query->orWhere('users.email', 'LIKE', '%'. $request->search .'%');
                });

            }
    

            $orderCount = clone $order;
            $totalRecords = $orderCount->count(DB::raw('DISTINCT(orders.id)'));  
            $order = $order->offset($offset)->limit($perPage)->get();       
            $order = new LengthAwarePaginator($order, $totalRecords, $perPage, $pageNo, [
                      'path'  => $request->url(),
                      'query' => $request->query(),
                    ]);
            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $order->setPath(route('orders.index'));
            $data['html'] = view('order.table', compact('order', 'perPage'))
                      ->with('i', $pageNo * $perPage)
                      ->render();

            return response($data);
        }
        return view('order.index');
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

        $validator = Validator::make($request->all(), [
            'order_id' =>'required',
            'prize'=>'required_if:type,prize',
            'amount'=>'required_if:type,amount',
            'coupon_id'=>'required_if:type,coupon',
            'link'=>'required_if:type,link',
            'type' =>'required'
        ]);
       
        if($validator->fails()){
            return redirect(route("giveaways.show",["giveaway"=> $request->giveaway_id ]))->withInput()->withErrors($validator);
        }
        
        $winning = "none";
        if($request->type == 'prize'){
            $winning = $request->prize;
        }

        if($request->type == 'amount'){
            $winning = $request->amount;
        }

        if($request->type == 'coupon'){
            $winning = $request->coupon_id;
        }
        if($request->type == 'link'){
            $winning = $request->link;
        }

        $order = Order::where('id',$request->order_id)->first();
        if(empty($order->winning)){
            $order->winning_type = $request->type;
            $order->winning      = $winning;
            $order->save();
            return redirect(route("giveaways.show",["giveaway"=> $request->giveaway_id ]))->withSuccess("Wiining has been saved successfully");
        }else{
            return redirect(route("giveaways.show",["giveaway"=> $request->giveaway_id ]))->withError("Wiiner can not be changed");
        }
     
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
       $order = Order::with('user','giveaway','quiz','spinner')->where('id',$order->id)->first(); 
       return view('order.view',compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
