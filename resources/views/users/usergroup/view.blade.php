@extends('layouts.admin')

@section('title')
Admin | UserGroup Details
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>UserGroup / Details</h4>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body mt-3">

                    <div class="d-flex justify-content-end">
                        {{-- Back Button --}}
                        <a href="{{ route('usergroup.index') }}" class="btn btn-outline-primary btn-pill btn-streach font-book fs-14 me-2">
                            <i class="fa fa-angle-left me-1"></i> Back
                        </a>

                        {{-- Edit Button --}}

                       
                    </div>

                    <h3 class="border-bottom mb-4">{{ $role->name }}</h3>

                    <div class="info-container">
                        <div class="row">   
                            <div class="col-6">
                                <ul class="list-unstyled justify-content-between">
                                    <li class="mb-3">
                                        <span class="fw-medium me-2">Name:</span> 
                                        <span>{{ $role->name }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-medium me-2">Guard Name:</span> 
                                        <span>{{ $role->guard_name }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-medium me-2">Created At:</span> 
                                        <span>{{ $role->created_at->format('d M Y, h:i A') }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-6">
                                <ul class="list-unstyled justify-content-between">
                                    {{-- <li class="mb-3">
                                        <span class="fw-medium me-2">Updated At:</span> 
                                        <span>{{ $role->updated_at->format('d M Y, h:i A') }}</span>
                                    </li> --}}
                                    {{-- <li class="mb-3">
                                        <span class="fw-medium me-2">Permissions:</span>
                                        @if($role->permissions && $role->permissions->count() > 0)
                                            @foreach($role->permissions as $permission)
                                                <span class="badge bg-primary me-1">{{ $permission->name }}</span>
                                            @endforeach
                                        @else
                                            <span>-</span>
                                        @endif
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                    </div><!-- /.info-container -->

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
