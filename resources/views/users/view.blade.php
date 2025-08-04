@extends('layouts.admin')

@section('title')
Admin | User Details
@endsection
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>User</h4>
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
                  @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin') )
                      <a href="{{route("users.edit",["user"=> $user->id ])}}" class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                  @endif    
                      <a href="{{route("users.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                <h5 class="pb-2 border-bottom mb-4">User Details</h5>
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
                            <span class="fw-medium me-2">DOB:</span>
                            <span>{{$user->dob ? dateFormat($user->dob) : ''}}</span>
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
                            <span class="fw-medium me-2">Gender:</span>
                            <span>{{ $user->gender ?? '' }}</span>
                          </li>
                           <li class="mb-3">
                            <span class="fw-medium me-2">Place:</span>
                            <span>{{ $user->place ?? ''}}</span>
                          </li>
                        </ul>
                    </div>

                    <div class="col-6"> 
                        <ul class="list-unstyled justify-content-between">  
                          <li class="mb-3">
                            <span class="fw-medium me-2">Street:</span>
                            <span>{{$user->street ?? ''}}</span>
                          </li> 
                          <li class="mb-3">
                            <span class="fw-medium me-2">Zipcode:</span>
                            <span>{{ $user->zipcode ?? '' }}</span>
                          </li>
                          <li class="mb-3">
                            <span class="fw-medium me-2">City:</span>
                            <span>{{ $user->city ?? '' }}</span>
                          </li>
                           <li class="mb-3">
                            <span class="fw-medium me-2">State:</span>
                            <span>{{ $user->state ?? '' }}</span>
                          </li>

                           <li class="mb-3">
                            <span class="fw-medium me-2">Country:</span>
                            <span>{{ $user->country ?? '' }}</span>
                          </li>
                          <li class="mb-3">
                            <span class="fw-medium me-2">Referral coupon:</span>
                            <span>{{ $user->referral_coupon?? '' }}</span>
                          </li>  
                        </ul>
                    </div>

                 <div class="d-flex pt-3 justify-content-end">
                  @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin') )    
                      <a href="{{route("users.edit",["user"=> $user->id ])}}" class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                  @endif    
                      <a href="{{route("users.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                </div>

                <div class="row">
                  <div class="col-12">
                    @if(!empty($user->loginLogs))  
                    <div class="card">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                          <i class="bi bi-box-arrow-in-right"></i> Login Activity
                        </h4>
                        
                      </div>
                      <div class="card-body">

                        <div class="list-group list-group-flush">
                            @foreach($user->loginLogs as $log)

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                              <div>
                                <i class="bi bi-alarm text-primary me-2"></i>
                                <strong>Logged</strong>
                               On  {{ $log->created_at->format('M d, Y') }}, {{ $log->created_at->format('h:i A') }}
                              </div>
                              <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            </div>
                            @endforeach
                          
                        </div>
                      </div>
                    </div>
                    @endif
                    </div>
                </div>
              </div>
          </div>
		</div>
    </div>
</div>
@endsection
