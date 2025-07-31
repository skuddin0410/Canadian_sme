@extends('layouts.admin')

@section('title')
Admin | Banner Details
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>Banner</h4>
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
                    @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Content Manager'))
                      <a href="{{route("banners.edit",["banner"=> $banner->id ])}}" class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                    @endif  
                      <a href="{{route("banners.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                  </div>
                <h5 class="pb-2 border-bottom mb-4">Banner Details</h5>
                <div class="info-container">
                  <ul class="list-unstyled">
                    <li class="mb-3">
                      <span class="fw-medium me-2">Title:</span>
                      <span>{{ $banner->name ?? '' }}</span>
                    </li>
                    <li class="mb-3">
                      <span class="fw-medium me-2">Link:</span>
                      <span>{{ $banner->link ?? '' }}</span>
                    </li>
                    <li class="mb-3">
                      <span class="fw-medium me-2">Order:</span>
                      <span>{{ $banner->order ?? '' }}</span>
                    </li>
                    <li class="mb-3">
                      <span class="fw-medium me-2">Image:</span>
                      @if(!empty($banner->photo) && $banner->photo->file_path)
                      <span class="fw-medium me-2"><img src="{{asset($banner->photo->file_path)  ?? ''}}" alt="banner Image" height="100px;"></span>
                      @endif
                    </li>
                    <li class="mb-3">
                      <span class="fw-medium me-2">Description:</span>
                      <span>{!! $banner->description !!}</span>
                    </li>
                    
                  </ul>
                  <div class="d-flex pt-3 justify-content-end">
                    @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Content Manager'))
                      <a href="{{route("banners.edit",["banner"=> $banner->id ])}}" class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                    @endif  
                      <a href="{{route("banners.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                  </div>
                </div>
              </div>
          </div>
		</div>
    </div>
</div>
@endsection
