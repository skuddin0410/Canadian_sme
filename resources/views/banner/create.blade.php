@extends('layouts.admin')

@section('title')
    Admin | Banners
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Banners</span></h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Banner @if(!empty($banner)) Update @else Create @endif</h5>
        </div>
        <div class="card-body">
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
       
          @if(!empty($banner))
             <form  action="{{route('banners.update',["banner"=>$banner->id])}}" method="POST" enctype="multipart/form-data">
          @else
             <form  action="{{route('banners.store')}}" method="POST" enctype="multipart/form-data">
          @endif 

          
            {{ csrf_field() }}
             <div class="mb-3">
              <label class="form-label" for="title">Name<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="name"
                  id="name"
                  value="{{ $banner->name ?? old('name') }}"
                  placeholder="Name"/>
              </div>
              @if ($errors->has('name'))
                <span class="text-danger text-left">{{ $errors->first('name') }}</span>
              @endif
            </div>
            <div class="mb-3">
              <label class="form-label" for="description">Description<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge" id="quill-editor" style="height: 300px;">
                <textarea
                  type="text"
                  name="description"
                  id="description"
                  class="form-control"
                  placeholder="Banner Description"
                  rows="8" cols="50"
                >{{ $banner->description ?? old('description') }}</textarea>
              </div>
              @if ($errors->has('description'))
                <span class="text-danger text-left">{{ $errors->first('description') }}</span>
              @endif
            </div>

             <div class="mb-3">
              <label class="form-label" for="title">Link<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="link"
                  id="link"
                  value="{{ $banner->link ?? old('link') }}"
                  placeholder="Link"/>
              </div>
              @if ($errors->has('link'))
                <span class="text-danger text-left">{{ $errors->first('link') }}</span>
              @endif
            </div> 
            <div class="mb-3">
              @if(!empty($banner->photo) && $banner->photo->file_path)
                <span class="fw-medium me-2">Image:</span>
                <span class="fw-medium me-2"><img src="{{asset($banner->photo->file_path)  ?? ''}}" alt="Blog Image" height="100px;"></span>
              @endif
            </div
            >

            <div class="mb-3">
              <label class="form-label" for="title">Image<span class="text-danger">*</span><span class="text-danger">(Allowed file size : {{config('app.banner_image_size')." KB and allowed file type ".config('app.image_mime_types') }}) </span> </label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="file"
                  class="form-control"
                  name="image"
                  id="image"/>
              </div>
              @if ($errors->has('image'))
                <span class="text-danger text-left">{{ $errors->first('image') }}</span>
              @endif
            </div>


            @if(!empty($banner))
             @method('PUT')
            @endif
             <div class="d-flex pt-3 justify-content-end">
             <a href="{{route('banners.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>  
            <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user">Save</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
