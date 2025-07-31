@extends('layouts.admin')

@section('title')
  Admin | Withdrawal Details
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>Withdrawal</h4>
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
                      <a href="{{route("withdrawals-request")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                <h5 class="pb-2 border-bottom mb-4">Withdrawal Details</h5>
                <div class="info-container">
                <div class="row">  
                    <div class="col-6"> 
                        <ul class="list-unstyled justify-content-between">
                          <li class="mb-3">
                            <span class="fw-medium me-2">Name:</span>
                            <span>{{ $withdrawal->user->name }} {{ $withdrawal->user->lastname }}</span>
                          </li>
                          <li class="mb-3">
                            <span class="fw-medium me-2">DOB:</span>
                            <span>{{$withdrawal->user->dob ? dateFormat($withdrawal->user->dob) : ''}}</span>
                          </li> 
                          <li class="mb-3">
                            <span class="fw-medium me-2">Email:</span>
                            <span>{{ $withdrawal->user->email }}</span>
                          </li>
                          <li class="mb-3">
                            <span class="fw-medium me-2">Mobile:</span>
                            <span>{{ $withdrawal->user->mobile ?? '' }}</span>
                          </li>
                        </ul>
                    </div>

                    <div class="col-6"> 
                        <ul class="list-unstyled justify-content-between">  
  
                          <li class="mb-3">
                            <span class="fw-medium me-2">Referral coupon:</span>
                            <span>{{ $withdrawal->user->referral_coupon?? '' }}</span>
                          </li> 
                           <li class="mb-3">
                            @if($withdrawal->user->kyc_verified == 1)
                            <span><button class="btn btn-outline-success">KYC: Approved </button></span>
                            @else
                             <span><button class="btn btn-outline-danger">KYC: Pending </button></span>
                            @endif
                          </li>   
                        </ul>
                    </div>
                    <div class="col-6">
                      <div class="row">
                        <h5 class="pb-2 pt-2 border-bottom border-top mb-4">Bank details</h5>
                        @if($withdrawal->user->bank)
                        <ul class="list-unstyled justify-content-between"> 
                          <li class="mb-3">
                            <span class="fw-medium me-2">Name:</span>
                            <span>{{$withdrawal->user->bank->holder ?? ''}}</span>
                          </li>
                          <li class="mb-3">
                            <span class="fw-medium me-2">UPI:</span>
                            <span>{{$withdrawal->user->bank->upi ?? ''}}</span>upi
                          </li>   
                          <li class="mb-3">
                            <span class="fw-medium me-2">Account No:</span>
                            <span>{{$withdrawal->user->bank->account ?? ''}}</span>
                          </li> 

                          <li class="mb-3">
                            <span class="fw-medium me-2">IFSC:</span>
                            <span>{{$withdrawal->user->bank->ifsc ?? ''}}</span>
                          </li> 
                        </ul>
                        @endif
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="row">
                        <h5 class="pb-2 pt-2 border-bottom border-top mb-4">Withdrawal Request</h5>
                        @if($withdrawal)
                        <ul class="list-unstyled justify-content-between"> 
                          <li class="mb-3">
                            <span class="fw-medium me-2">Reference:</span>
                            <span>{{$withdrawal->reference ?? ''}}</span>
                          </li>
                          <li class="mb-3">
                            <span class="fw-medium me-2">Amount:</span>
                            <span>{{config('app.currency_sign')}}{{$withdrawal->amount ?? ''}}</span>
                          </li>   
                          <li class="mb-3">
                            <span class="fw-medium me-2">Status:</span>
                            @if($withdrawal->status == 'init')
                            <span class="btn btn-outline-warning btn-sm">Requested</span>
                            @endif
                            @if($withdrawal->status == 'success')
                            <span class="btn btn-outline-success btn-sm">Success</span>
                            @endif
                            @if($withdrawal->status == 'failed')
                            <span class="btn btn-outline-danger btn-sm">Failed</span>
                            @endif
                          </li> 

                          <li class="mb-3">
                            <span class="fw-medium me-2">Requested At:</span>
                            <span>{{dateFormat($withdrawal->created_at) ?? ''}}</span>
                          </li> 
                        </ul>
                        @endif
                      </div>
                    </div>
                
                 <div class="d-flex pt-3 justify-content-end">
                      <a href="{{route("withdrawals-request")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                </div>
              </div>
          </div>
		</div>
    </div>
</div>
@endsection
