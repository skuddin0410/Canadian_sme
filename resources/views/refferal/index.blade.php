@extends('layouts.admin')

@section('title')
    Admin | Refferal User List
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Refferal User/</span>Lists</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
				    <h5 class="mb-0">Refferal Users</h5>
				<div class="d-flex pt-3 justify-content-end">
	                <a href="{{route("users.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
				</div>

				<div class="card-body pt-0">
					@if(Session::has('success'))
		              <div class="alert alert-success">
		              {{ Session::get('success') }}
		              </div>
		          @endif
		          @if(Session::has('error'))
		              <div class="alert alert-danger">
		              {{ Session::get('error') }}
		              </div>
		          @endif
					{{-- <p class="text-left font-bold mb-2 total_datas">Total No of User: 0</p> --}}
					<table id="post-manager" class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
					<thead>
						<tr>
							<th>Photo</th>
							<th>Name</th>
							<th>Referral coupon</th>
							<th>Email</th>
							<th>Mobile</th>
							<th>DOB</th>
							<th>Gender</th>
							<th>Country</th>
							<th>Place</th>
							<th>Street</th>
							<th>Zipcode</th>
						</tr>
                        @foreach($users as $val)
					    <tr>
					    	@if(!empty($val->user->photo) && $val->user->photo->file_path)
                           <th><img src="{{asset($val->user->photo->file_path)  ?? ''}}" alt="User Image" height="30px;"></th>
                            @else
                            <th></th>
                            @endif
					    	<th>{{$val->user->name ?? ''}} {{$val->user->lastname ?? ''}}</th>
							<th>{{$val->user->referral_coupon ?? ''}}</th>
							<th>{{$val->user->email ?? ''}}</th>
							<th>{{$val->user->mobile ?? ''}}</th>
							<th>{{$val->user->dob ? dateFormat($val->user->dob) : '' }}</th>
							<th>{{$val->user->gender ?? '' }}</th>
							<th>{{$val->user->country ?? ''}}</th>
							<th>{{$val->user->place ?? ''}}</th>
							<th>{{$val->user->street ?? ''}}</th>
							<th>{{$val->user->zipcode ?? ''}}</th>
						</tr>
						@endforeach
						@if(count($users) <=0)
						    <tr>
                              <td colspan="14">No data available</td>
                            </tr>
						@endif
					</thead>
					</table>
					<div class="d-flex pt-3 justify-content-end">
	                {{ $users->links('pagination::bootstrap-4') }}
	                </div>
	             <div class="d-flex pt-3 justify-content-end">
	                <a href="{{route("users.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
		        </div>

			</div>
		</div>
    </div>
</div>
@endsection
