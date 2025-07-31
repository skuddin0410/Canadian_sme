@extends('layouts.admin')

@section('title')
  Admin | Order Details
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>Order</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div class="dt-action-buttons text-end pt-3 pt-md-0">
                  <div class="dt-buttons"> 
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex pt-3 justify-content-end">
                      <a href="{{route("orders.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                <h5 class="pb-2 border-bottom mb-4">Order Details</h5>
                <div class="info-container">
                <div class="row">  
                    <div class="col-6"> 
                        <ul class="list-unstyled justify-content-between">
                          <li class="mb-3">
                            <span class="fw-medium me-2">Name:</span>
                            <span>{{ $order->user->name }} {{ $order->user->lastname }}</span>
                          </li>
                          <li class="mb-3">
                            <span class="fw-medium me-2">DOB:</span>
                            <span>{{$order->user->dob ? dateFormat($order->user->dob) : ''}}</span>
                          </li> 
                          <li class="mb-3">
                            <span class="fw-medium me-2">Email:</span>
                            <span>{{ $order->user->email }}</span>
                          </li>
                          <li class="mb-3">
                            <span class="fw-medium me-2">Mobile:</span>
                            <span>{{ $order->user->mobile ?? '' }}</span>
                          </li>
                        </ul>
                    </div>

                    <div class="col-6"> 
                        <ul class="list-unstyled justify-content-between">  
  
                          <li class="mb-3">
                            <span class="fw-medium me-2">Referral coupon:</span>
                            <span>{{ $order->user->referral_coupon?? '' }}</span>
                          </li> 
                           <li class="mb-3">
                            @if($order->user->kyc_verified == 1)
                            <span><button class="btn btn-outline-success">KYC: Approved </button></span>
                            @else
                             <span><button class="btn btn-outline-danger">KYC: Pending </button></span>
                            @endif
                          </li>   
                        </ul>
                    </div>
                
                 <div class="d-flex pt-3 justify-content-end">
                      <a href="{{route("orders.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                </div>
              </div>
          </div>
		</div>
    </div>
</div>
@endsection
