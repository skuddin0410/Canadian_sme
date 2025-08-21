@extends('layouts.admin')

@section('title')
Admin |  Admin user Details
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span> Admin user</h4>
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
                      <a href="{{route("admin-users.edit",["admin_user"=> $user->id ])}}" class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                      <a href="{{route("admin-users.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                <h5 class="pb-2 border-bottom mb-4"> Admin user Details</h5>
                <div class="info-container">
                <div class="row">  
                    <div class="col-6"> 
                        <ul class="list-unstyled justify-content-between">
                          <li class="mb-3">
                            <span class="fw-medium me-2">Name:</span>
                            <span>{{ $user->name }} {{ $user->lastname }}</span>
                          </li>
                           <li class="mb-3">
                            <span class="fw-medium me-2">User Name:</span>
                            <span>{{ $user->username ?? ''}}</span>
                          </li>
                          <li class="mb-3">
                            <span class="fw-medium me-2">Email:</span>
                            <span>{{ $user->email }}</span>
                          </li>
                          <li class="mb-3">
                            <span class="fw-medium me-2">Mobile:</span>
                            <span>{{ $user->mobile ?? '' }}</span>
                          </li>
                          <li class="mb-3">
                            <span class="fw-medium me-2">Referral Coupon:</span>
                            <span>{{ $user->referral_coupon ?? '' }}</span>
                          </li>
              
                        </ul>
                    </div>
                
                 <div class="d-flex pt-3 justify-content-end">
      
                      {{-- <a href="{{route("admin-users.edit",["admin_user"=> $user->id ])}}" class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                      <a href="{{route("admin-users.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a> --}}
                </div>
                </div>
              </div>
          </div>
		</div>
    </div>
</div>
@endsection
