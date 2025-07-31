@extends('layouts.admin')

@section('title')
Admin | FAQ Details
@endsection

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
.checked {
  color: orange;
}
</style>
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>FAQ</h4>
 
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
                      <a href="{{route("faqs.edit",["faq"=> $faq->id ])}}" class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                   @endif  
                      <a href="{{route("faqs.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                <h5 class="pb-2 border-bottom mb-4">FAQ Details</h5>
                <div class="info-container">
                  <ul class="list-unstyled">
                    <li class="mb-3">
                      <span class="fw-medium me-2">Question:</span>
                      <span>{{ $faq->name ?? '' }}</span>
                    </li>
       
                    <li class="mb-3">
                      <span class="fw-medium me-2">Answer:</span>
                      <span>{!! $faq->description !!}</span>
                    </li>

                    <li class="mb-3">
                      <span class="fw-medium me-2">Create At:</span>
                      <span>{{ dateFormat($faq->created_at) }}</span>
                    </li>

                  </ul>
                  <div class="d-flex pt-3 justify-content-end">
                    @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Content Manager'))
                      <a href="{{route("faqs.edit",["faq"=> $faq->id ])}}" class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                    @endif 
                      <a href="{{route("faqs.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                </div>
              </div>
          </div>
		</div>
    </div>
</div>
@endsection
