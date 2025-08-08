@extends('layouts.admin')

@section('title')
Admin | Testimonial Details
@endsection

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
.checked {
  color: orange;
}
</style>
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>Testimonial</h4>
 
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
                      <a href="{{route("testimonials.edit",["testimonial"=> $testimonial->id ])}}" class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                  @endif   
                      <a href="{{route("testimonials.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                <h5 class="pb-2 border-bottom mb-4">Testimonial Details</h5>
                <div class="info-container">
                  <ul class="list-unstyled">
                    <li class="mb-3">
                      <span class="fw-medium me-2">Title:</span>
                      <span>{{ $testimonial->name ?? '' }}</span>
                    </li>
       
                    <li class="mb-3">
                      <span class="fw-medium me-2">Message:</span>
                      <span>{!! $testimonial->message !!}</span>
                    </li>
                    <li class="mb-3">
                      <span class="fw-medium me-2">rating:</span>
                      @for($i=1;$i<=5;$i++)
                       <span class="fa fa-star {{ !empty($testimonial->rating) && $testimonial->rating >= $i ? 'checked' : '' }}"></span>
                
                @endfor
                    </li>

                    <li class="mb-3">
                      <span class="fw-medium me-2">Photo:</span>
                      @if(!empty($testimonial->photo) && $testimonial->photo->file_path)
                      <span class="fw-medium me-2"><img src="{{asset($testimonial->photo->file_path)  ?? ''}}" alt="testimonial Image" height="100px;"></span>
                      @endif
                    </li>
 
                  </ul>
                  <div class="d-flex pt-3 justify-content-end">
                    @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))	
                      <a href="{{route("testimonials.edit",["testimonial"=> $testimonial->id ])}}" class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                    @endif 
                      <a href="{{route("testimonials.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                </div>
              </div>
          </div>
		</div>
    </div>
</div>
@endsection
