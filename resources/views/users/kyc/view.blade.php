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
                     
                      <a href="{{route("kyc-users")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                <h5 class="pb-2 border-bottom mb-4">User Details</h5>
                <div class="info-container">
                <div class="row">  
                    <div class="col-4"> 
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

                    <div class="col-4"> 
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
                           <li class="mb-3">
                            @if($user->kyc_verified == 1)
                            <span><button class="btn btn-outline-success">KYC: Approved </button></span>
                            @else
                             <span><button class="btn btn-outline-danger">KYC: Pending </button></span>
                            @endif
                          </li>   
                        </ul>
                    </div>

                    <div class="col-4"> 
                      <div class="row"> 
                          <div class="col-6"> 
                          <h5 class="pb-2 border-bottom mb-4">Aadhaar Front Side:</h5> 
                          @if(!empty($user->photo) && $user->photo->file_path)
                            <a href="{{asset($user->photo->file_path)}}" onclick="window.open(this.href, '_blank', 'left=20,top=20,width=500,height=500,toolbar=1,resizable=0'); return false;"><img src="{{asset($user->photo->file_path)  ?? ''}}" alt="User Image" width="100%"></a>
                          @endif
                          </div> 

                          <div class="col-6">
                          <h5 class="pb-2 border-bottom mb-4">Aadhaar Back Side:</h5>  
                          @if(!empty($user->background) && $user->background->file_path)
                            <a href="{{asset($user->background->file_path)}}" onclick="window.open(this.href, '_blank', 'left=20,top=20,width=500,height=500,toolbar=1,resizable=0'); return false;"><img src="{{asset($user->background->file_path)  ?? ''}}" alt="User Image" width="100%"></a>
                          @endif
                          </div>  

                      </div>  
                    </div>
                
                 <div class="d-flex pt-3 justify-content-end">
                    <a href="{{route("kycApproved",["id"=> $user->id,'status'=>1])}}" class="btn btn-outline-success btn-pill btn-streach font-book me-2 mt-10 fs-14">Approved Kyc</a>
                    <a href="#" data-toggle="modal" data-target="#myModal" class="btn btn-outline-danger btn-pill btn-streach font-book me-2 mt-10 fs-14">Reject Kyc</a>
                      <a href="{{route("kyc-users")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                </div>
              </div>
          </div>
		</div>
    </div>
    
    <div class="modal" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">KYC Rejected <span class="text-danger">*</span></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <form action="{{route("kycRejected",["id"=> $user->id,'status'=>0])}}" method="GET"> 
          @csrf
        <div class="modal-body">
          <input type="text" value="" name="reasons" id="reasons" class="form-control" placeholder="Enter rejection reasons" maxlength="200" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-outline-danger">Reject</button>
          <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
        </div>
        </form> 
      </div>
    </div>
</div>
@endsection
