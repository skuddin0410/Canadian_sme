<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use DB;
class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {     $perPage = (int) $request->input('perPage', 20);
          $pageNo = (int) $request->input('page', 1);
          $offset = $perPage * ($pageNo - 1);
          $search = (int) $request->input('search', '');
        if($request->ajax() && $request->ajax_request == true){
            $coupons = Coupon::with('spinners')->orderBy('id','DESC');
            if($request->search){
            $coupons = $coupons->where(function($query) use($request){
                    $query->where('name', 'LIKE', '%'. $request->search .'%');
                });
            }
            $couponsCount = clone $coupons;
            $totalRecords = $couponsCount->count(DB::raw('DISTINCT(coupons.id)'));  
            $coupons = $coupons->offset($offset)->limit($perPage)->get();       
            $coupons = new LengthAwarePaginator($coupons, $totalRecords, $perPage, $pageNo, [
                      'path'  => $request->url(),
                      'query' => $request->query(),
                    ]);
            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $coupons->setPath(route('coupons.index'));
            $data['html'] = view('coupon.table', compact('coupons', 'perPage'))
                      ->with('i', $pageNo * $perPage)
                      ->render();

            return response($data);

        }
        return view('coupon.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("coupon.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|alpha_num|unique:coupons,name',
            'price'=>'required|numeric|gt:0',
            'expires_at' => 'required|date',
            'type' =>'required'
        ]);

        if($validator->fails()){
            return redirect(route('coupons.create'))->withInput()->withErrors($validator);
        }

        $coupon = new Coupon();
        $coupon->name = $request->name;
        $coupon->price = $request->price; 
        $coupon->expires_at = $request->expires_at;
        $coupon->type = $request->type;
        $coupon->save();
        return redirect(route('coupons.index'))->withSuccess("Coupon has been saved successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        return view("coupon.view",["coupon" => $coupon]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        return view("coupon.create",["coupon" => $coupon]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|alpha_num|unique:coupons,name,'.$coupon->id,
            'price'=>'required|numeric|gt:0',
            'expires_at' => 'required|date',
            'type' =>'required'
        ]);

        if($validator->fails()){
            return redirect(route('coupons.edit',["coupon"=>$coupon->id]))->withInput()->withErrors($validator);
        }

        $coupon->name = $request->name;
        $coupon->price = $request->price; 
        $coupon->expires_at = $request->expires_at;
        $coupon->type = $request->type;
        $coupon->save();
        return redirect(route('coupons.index'))->withSuccess("Coupon has been saved successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect(route('coupons.index'))->withSuccess("Coupon code deleted successfully");
    }
}
