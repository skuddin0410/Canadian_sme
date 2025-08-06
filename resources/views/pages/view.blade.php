@extends('layouts.admin')

@section('title')
Admin | CMS Page Details
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">CMS/</span>Page</h4>
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
                 @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
                 <a href="{{route("pages.edit",["page"=> $page->id ])}}" class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                 @endif
                  <a href="{{route("pages.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                <h5 class="pb-2 border-bottom mb-4">CMS Page Details</h5>
                <div class="info-container">
                  <ul class="list-unstyled">
                    <li class="mb-3">
                      <span class="fw-medium me-2">Title:</span>
                      <span>{{ $page->name }}</span>
                    </li>
                    <li class="mb-3">
                      <span class="fw-medium me-2">Slug:</span>
                      <span>{{ $page->slug }}</span>
                    </li>
        
                    <li class="mb-3">
                      <span class="fw-medium me-2">Tags:</span>
                      <span>{{ $page->tags }}</span>
                    </li>
                    <li class="mb-3">
                      <span class="fw-medium me-2">Description:</span>
                      <span>{!! $page->description !!}</span>
                    </li>
                    <li class="mb-3">
                      <span class="fw-medium me-2">Image:</span>
                      @if(!empty($page->photo) && $page->photo->file_path)
                      <span class="fw-medium me-2"><img src="{{asset($page->photo->file_path)  ?? ''}}" alt="page Image" height="100px;"></span>
                      @endif
                    </li>
                    <li class="mb-3">
                      <span class="fw-medium me-2">Meta Title:</span>
                      <span>{{ $page->meta_title }}</span>
                    </li>
                    <li class="mb-3">
                      <span class="fw-medium me-2">Meta Description:</span>
                      <span>{!! $page->meta_description !!}</span>
                    </li>
                    <li class="mb-3">
                      <span class="fw-medium me-2">Meta Keywords:</span>
                      <span>{{ $page->meta_keywords }}</span>
                    </li>
                  </ul>
                  <div class="d-flex pt-3 justify-content-end">
                    @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
                      <a href="{{route("pages.edit",["page"=> $page->id ])}}" class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                    @endif  
                      <a href="{{route("pages.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                  </div>
                </div>
              </div>
          </div>
		</div>
    </div>
</div>
@endsection
