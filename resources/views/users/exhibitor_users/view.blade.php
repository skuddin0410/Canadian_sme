@extends('layouts.admin')

@section('title')
Admin | Exhibitor User Details
@endsection

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>Exhibitor User</h4>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="dt-action-buttons text-end pt-3 pt-md-0">
                        <div class="dt-buttons"></div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="d-flex pt-3 justify-content-end">
                        <a href="{{ route('exhibitor-users.index') }}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                    </div>
                    <div class="d-flex pt-3 justify-content-end">
            @if(!$user->is_approve)
    <form action="{{ route('exhibitor-users.approve', $user->id) }}" method="POST" style="display: inline-block;">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-success">
            Approve Exhibitor
        </button>
    </form>
        @else
            <span class="badge bg-success">Approved</span>
        @endif
    </div>

                    <h5 class="pb-2 border-bottom mb-4">Exhibitor User Details</h5>

                    <div class="info-container">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-4">
                                <ul class="list-unstyled justify-content-between">
                                    <li class="mb-3"><span class="fw-medium me-2">Name:</span> <span>{{ $user->name }} {{ $user->lastname }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">User Name:</span> <span>{{ $user->username ?? '' }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">DOB:</span> <span>{{ $user->dob ? dateFormat($user->dob) : '' }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">Email:</span> <span>{{ $user->email }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">Mobile:</span> <span>{{ $user->mobile ?? '' }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">Gender:</span> <span>{{ $user->gender ?? '' }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">Place:</span> <span>{{ $user->place ?? '' }}</span></li>
                                </ul>
                            </div>

                            <!-- Right Column -->
                            <div class="col-4">
                                <ul class="list-unstyled justify-content-between">
                                    <li class="mb-3"><span class="fw-medium me-2">Street:</span> <span>{{ $user->street ?? '' }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">Zipcode:</span> <span>{{ $user->zipcode ?? '' }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">City:</span> <span>{{ $user->city ?? '' }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">State:</span> <span>{{ $user->state ?? '' }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">Country:</span> <span>{{ $user->country ?? '' }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">Referral coupon:</span> <span>{{ $user->referral_coupon ?? '' }}</span></li>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{--  Booth Details Section --}}
                    <div class="mt-5">
                        <h5 class="pb-2 border-bottom mb-3">Booth Details</h5>
                        @if ($user->booths && $user->booths->count())
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Booth Name</th>
                                        <th>Booth Number</th>
                                        <th>Hall Name</th>
                                        {{-- <th>Status</th> --}}
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user->booths as $booth)
                                        <tr>
                                            <td>{{ $booth->name ?? '-' }}</td>
                                            <td>{{ $booth->booth_number ?? '-' }}</td>
                                            <td>{{ $booth->hall_name ?? '-' }}</td>
                                            {{-- <td>
                                                <span class="badge badge-{{ $booth->status == 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($booth->status) }}
                                                </span>
                                            </td> --}}
                                            <td>{{ \Carbon\Carbon::parse($booth->created_at)->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">No booths assigned to this user.</p>
                        @endif
                    </div>
                         @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
                         
                         <form method="POST" action="{{ route('exhibitor-users.assign-booth', $user->id) }}">
                          @csrf
                        <div class="row align-items-end">
                         <div class="col-md-4">
                         <label for="booth_id" class="form-label">Assign Booth:</label>
                                <select name="booth_id" class="form-control" required>
                                <option value="" disabled selected>Select a booth</option>
                                @foreach ($booths as $booth)
                                 
                                
                                 @endforeach
                                </select>
                         </div>
                         <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-success">Assign Booth</button>
                         </div>
                        </div>
                         </form>
                        
                    @endif


                </div>
            </div>
            

    </div>
    </div>
</div>
@endsection
