@extends('layouts.admin')

@section('title', 'Admin | Exhibitor User Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h3 class="py-3 mb-4">Exhibitor User</h3>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body">

                    {{-- Action Buttons --}}
                   {{-- Action Buttons --}}
<div class="d-flex justify-content-end gap-2 pt-3">
    <a href="{{ route('exhibitor-users.index') }}" 
       class="btn btn-outline-primary btn-pill btn-streach font-book fs-14">Back</a>
</div>
       <div class="d-flex justify-content-end gap-2 pt-3">
    {{-- Block Button for Admin / Event Admin --}}
    @if(Auth::user()->hasAnyRole(['Admin','Event Admin']) 
        && !$user->is_block 
        && $user->hasAnyRole(['Admin','Representative','Attendee','Speaker']))
        <form action="{{ route('users.toggleBlock', $user->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger btn-pill btn-streach font-book fs-14">
                Block User
            </button>
        </form>
    @endif

    {{-- Unblock Button for Support Staff / Helpdesk --}}
    @if(Auth::user()->hasRole('Support Staff Or Helpdesk') && $user->is_block)
        <form action="{{ route('helpdesk.users.unblock', $user->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success btn-pill btn-streach font-book fs-14">
                Unblock User
            </button>
        </form>
    @endif

    {{-- Approve Exhibitor Button --}}
    @if(Auth::user()->hasAnyRole(['Admin','Event Admin']) && !$user->is_block)
        @if(!$user->is_approve)
            <form action="{{ route('exhibitor-users.approve', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success">
                    Approve Exhibitor
                </button>
            </form>
        @else
            <span class="badge bg-success">Approved</span>
        @endif
    @endif
        </div>
</div>

                    {{-- User Details --}}
                    <h5 class="pb-2 border-bottom mt-4 mb-4">Exhibitor User Details</h5>
                    <div class="row">
                        <div class="col-4">
                            <ul class="list-unstyled">
                                <li class="mb-3"><strong>Name:</strong> {{ $user->name }} {{ $user->lastname }}</li>
                                <li class="mb-3"><strong>User Name:</strong> {{ $user->username ?? '' }}</li>
                                <li class="mb-3"><strong>DOB:</strong> {{ $user->dob ? dateFormat($user->dob) : '' }}</li>
                                <li class="mb-3"><strong>Email:</strong> {{ $user->email }}</li>
                                <li class="mb-3"><strong>Mobile:</strong> {{ $user->mobile ?? '' }}</li>
                                <li class="mb-3"><strong>Gender:</strong> {{ $user->gender ?? '' }}</li>
                                <li class="mb-3"><strong>Place:</strong> {{ $user->place ?? '' }}</li>
                            </ul>
                        </div>

                        <div class="col-4">
                            <ul class="list-unstyled">
                                <li class="mb-3"><strong>Street:</strong> {{ $user->street ?? '' }}</li>
                                <li class="mb-3"><strong>Zipcode:</strong> {{ $user->zipcode ?? '' }}</li>
                                <li class="mb-3"><strong>City:</strong> {{ $user->city ?? '' }}</li>
                                <li class="mb-3"><strong>State:</strong> {{ $user->state ?? '' }}</li>
                                <li class="mb-3"><strong>Country:</strong> {{ $user->country ?? '' }}</li>
                                <li class="mb-3"><strong>Referral coupon:</strong> {{ $user->referral_coupon ?? '' }}</li>
                            </ul>
                        </div>

                        <div class="col-4">
                            <h5>Company Details</h5>
                            @forelse($companies as $company)
                                @if($company->logoFile)
                                    <img src="{{ Storage::url($company->logoFile->file_name) }}" 
                                         class="card-img-top mb-2" style="height: 100px; object-fit: cover;">
                                @endif

                                <p><strong>Name:</strong> {{ $company->name }}</p>
                                <p><strong>Industry:</strong> {{ $company->industry }}</p>
                                <p><strong>Size:</strong> {{ $company->size }}</p>
                                <p><strong>Location:</strong> {{ $company->location }}</p>
                                <p><strong>Email:</strong> {{ $company->email }}</p>
                                <p><strong>Phone:</strong> {{ $company->phone }}</p>
                                <p><strong>Certifications:</strong> {{ $company->certifications ?? '-' }}</p>

                                @if($company->certificationFile)
                                    <h6>Certification</h6>
                                    <img src="{{ Storage::url($company->certificationFile->file_name) }}" 
                                         class="img-fluid rounded mb-2" style="max-height: 150px; object-fit: cover;">
                                @endif

                                @if($company->mediaGallery->count())
                                    <h6>Media Gallery</h6>
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        @foreach($company->mediaGallery as $media)
                                            <img src="{{ Storage::url($media->file_name) }}" 
                                                 style="width: 70px; height: 70px; object-fit: cover;" class="rounded">
                                        @endforeach
                                    </div>
                                @endif

                                @if($company->videos->count())
                                    <h6>Videos</h6>
                                    @foreach($company->videos as $video)
                                        <video controls style="width: 100%; max-height: 200px;" class="mb-2">
                                            <source src="{{ Storage::url($video->file_name) }}" type="video/mp4">
                                        </video>
                                    @endforeach
                                @endif
                            @empty
                                <p class="text-muted">No companies registered by this exhibitor.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Booth Details --}}
                    {{-- <div class="mt-5">
                        <h5 class="pb-2 border-bottom mb-3">Booth Details</h5>
                        @if ($user->booths->count())
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Booth Name</th>
                                        <th>Booth Number</th>
                                        <th>Hall Name</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user->booths as $booth)
                                        <tr>
                                            <td>{{ $booth->name ?? '-' }}</td>
                                            <td>{{ $booth->booth_number ?? '-' }}</td>
                                            <td>{{ $booth->hall_name ?? '-' }}</td>
                                            <td>{{ $booth->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">No booths assigned to this user.</p>
                        @endif
                    </div> --}}
                    {{-- Booth Details --}}
<div class="mt-5">
    <h5 class="pb-2 border-bottom mb-3">Booth Details</h5>
    @if ($user->booths->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Booth Number</th>
                    <th>Size</th>
                    <th>Location Preferences</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user->booths as $booth)
                    <tr>
                        <td>{{ $booth->title ?? '-' }}</td>
                        <td>{{ $booth->booth_number ?? '-' }}</td>
                        <td>{{ $booth->size ?? '-' }}</td>
                        <td>{{ $booth->location_preferences ?? '-' }}</td>
                        <td>{{ $booth->created_at->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">No booths assigned to this user.</p>
    @endif
</div>

{{-- Assign Booth Form --}}
@if(Auth::user()->hasAnyRole(['Admin','Event Admin']))
    <form method="POST" action="{{ route('exhibitor-users.assign-booth', $user->id) }}" class="mt-3">
        @csrf
        <div class="row align-items-end">
            <div class="col-md-4">
                <label for="booth_id" class="form-label">Assign Booth:</label>
                <select name="booth_id" class="form-control" required>
                    <option value="" disabled selected>Select a booth</option>
                    @foreach ($booths as $booth)
                        <option value="{{ $booth->id }}">
                            {{ $booth->title ?? 'Booth #'.$booth->booth_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success">Assign Booth</button>
            </div>
        </div>
    </form>
@endif


                    {{-- Assign Booth Form --}}
                    {{-- @if(Auth::user()->hasAnyRole(['Admin','Event Admin']))
                        <form method="POST" action="{{ route('exhibitor-users.assign-booth', $user->id) }}" class="mt-3">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label for="booth_id" class="form-label">Assign Booth:</label>
                                    <select name="booth_id" class="form-control" required>
                                        <option value="" disabled selected>Select a booth</option>
                                        @foreach ($booths as $booth)
                                            <option value="{{ $booth->id }}">{{ $booth->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success">Assign Booth</button>
                                </div>
                            </div>
                        </form>
                    @endif --}}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
