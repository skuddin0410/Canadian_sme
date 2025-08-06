@extends('layouts.admin')

@section('title')
    Admin | Faqs
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">faqs</span></h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Faq @if(!empty($faq)) Update @else Create @endif</h5>
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
       
          @if(!empty($faq))
             <form  action="{{route('faqs.update',["faq"=>$faq->id])}}" method="POST" enctype="multipart/form-data">
          @else
             <form  action="{{route('faqs.store')}}" method="POST" enctype="multipart/form-data">
          @endif 

          
            {{ csrf_field() }}
             <div class="mb-3">
              <label class="form-label" for="title">Question<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="name"
                  id="name"
                  value="{{ $faq->name ?? old('name') }}"
                  placeholder="Name"/>
              </div>
              @if ($errors->has('name'))
                <span class="text-danger text-left">{{ $errors->first('name') }}</span>
              @endif
            </div>
            <div class="mb-3">
              <label class="form-label" for="description">Answer<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge" id="quill-editor" style="height: 300px;">
                <textarea
                  type="text"
                  name="description"
                  id="description"
                  class="form-control"
                  placeholder="Faq Answer"
                  rows="8" cols="50"
                >{{ $faq->description ?? old('description') }}</textarea>
              </div>
              @if ($errors->has('description'))
                <span class="text-danger text-left">{{ $errors->first('description') }}</span>
              @endif
            </div>

            @if(!empty($faq))
             @method('PUT')
            @endif
            <div class="d-flex pt-3 justify-content-end">
            <a href="{{route('faqs.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>  
            <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user">Save</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
