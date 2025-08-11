@extends('layouts.admin')

@section('title')
    Admin | Booth Add
@endsection

@section('content')
<style>.ql-editor{
     width: 100%;
  }
</style>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Add/</span>Booth</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Booth Create</h5>
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
          <form  action="{{ route('booths.store') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }} 
            <input type="hidden" name="company_id" value="{{Auth::user()->company->id}}">
            <div class="row">
            <div class="col-12">
             <div class="mb-3">
              <label class="form-label" for="title">Booth Title<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                
              </div>
              @if ($errors->has('title'))
                <span class="text-danger text-left">{{ $errors->first('title') }}</span>
              @endif
            </div>
          </div>
          <div class="col-6">
            <div class="mb-3">
                  <label class="form-label" for="title">Booth Number<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      name="booth_number"
                      id="booth_number-target"
                      value="{{ old('Booth number') }}"
                      placeholder="Booth number"
                      />
                  </div>
                  @if ($errors->has('booth_number'))
                    <span class="text-danger text-left">{{ $errors->first('booth_number') }}</span>
                  @endif
            </div>
           </div>

           <div class="col-6">
            <div class="mb-3">
                  <label class="form-label" for="title">Size<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      name="size"
                      id="size-target"
                      value="{{ old('size') }}"
                      placeholder="Size"
                      />
                  </div>
                  @if ($errors->has('size'))
                    <span class="text-danger text-left">{{ $errors->first('size') }}</span>
                  @endif
            </div>
           </div>
           </div>

            <div class="mb-3">
              <label class="form-label" for="description">Location Preferences<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge" id="quill-editor" style="height: 300px;">
                <textarea
                  type="text"
                  name="location_preferences"
                  id="location_preferences"
                  class="form-control"
                  placeholder="Post Description"
                  rows="8" cols="50"
                >{{ old('Location Preferences') }}</textarea>
              </div>
              @if ($errors->has('location_preferences'))
                <span class="text-danger text-left">{{ $errors->first('location_preferences') }}</span>
              @endif
            </div>

          <div class="d-flex pt-3 justify-content-end">
             <a href="{{route('booths.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user">Save</button>
          </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
  $("#slug-source").keyup(function() {
      var Text = $(this).val();
      Text = slugify(Text);
      $("#slug-target").val(Text);        
  });

  $("#slug-source").blur(function() {
      var Text = $(this).val();
      Text = slugify(Text);
      $("#slug-target").val(Text);        
  });
@endsection