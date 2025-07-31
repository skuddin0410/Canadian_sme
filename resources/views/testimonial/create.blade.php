@extends('layouts.admin')

@section('title')
    Admin | Testimonials
@endsection
@section('content')
<style>
  .ql-editor{
      width: 100%;
   }
 </style>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Testimonials</span></h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Testimonial @if(!empty($testimonial)) Update @else Create @endif</h5>
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
       
          @if(!empty($testimonial))
             <form  action="{{route('testimonials.update',["testimonial"=>$testimonial->id])}}" method="POST" enctype="multipart/form-data">
          @else
             <form  action="{{route('testimonials.store')}}" method="POST" enctype="multipart/form-data">
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
                  value="{{ $testimonial->name ?? old('name') }}"
                  placeholder="Name"/>
              </div>
              @if ($errors->has('name'))
                <span class="text-danger text-left">{{ $errors->first('name') }}</span>
              @endif
            </div>
            <div class="mb-3">
              <label class="form-label" for="description">Message<span class="text-danger">*</span></label>
              <input type="hidden" name="description" id="description" value="{{ $testimonial->message ?? old('description') }}">
              <div class="input-group input-group-merge" id="quill-editor" style="height: 300px;">
                <textarea
                  type="text"
                  name="quil-description"
                  id="quil-description"
                  class="form-control"
                  placeholder="Testimonial Message"
                  rows="8" cols="50"
                ></textarea>
              </div>
              @if ($errors->has('description'))
                <span class="text-danger text-left">{{ $errors->first('description') }}</span>
              @endif
            </div>


            <div class="mb-3">
              <label class="form-label" for="title">Image</label>
              <div class="input-group input-group-merge">
                @if(!empty($testimonial->photo) && $testimonial->photo->file_path)
                <img src="{{asset($testimonial->photo->file_path)  ?? ''}}" alt="testimonial Image" height="100px;">
                @endif
              </div>
            </div>
            <div class="mb-3">
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

            <div class="mb-3">
              <label class="form-label" for="title">Rating</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-chevron-down"></i></span>
                <select class="form-control" name="rating">
                  @for($i=1;$i<=5;$i++)
                  <option value="{{$i}}" {{ (!empty($testimonial) && $testimonial->rating == $i) ? "selected" : "" }}>{{$i}}</option>
                  @endfor
                </select>
              </div>
            </div>
           
            <div class="mb-3">
              <label class="form-label" for="title">Status</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-chevron-down"></i></span>
                <select class="form-control" name="status">
                  <option value="init" {{ (!empty($testimonial) && $testimonial->status == 'init') ? "selected" : "" }}>Inactive</option>
                  <option value="success" {{ (!empty($testimonial) && $testimonial->status == 'success') ? "selected" : "" }}>Active</option>
                </select>
              </div>
            </div>  

            @if(!empty($testimonial))
             @method('PUT')
            @endif
             <div class="d-flex pt-3 justify-content-end">
               <a href="{{route('testimonials.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
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
<script type="text/javascript">
     var form = document.querySelector("form");
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('quil-description')) {
            var editor = new Quill('#quill-editor', {
                theme: 'snow',
                modules: { 
                  toolbar: [['link']] 
                }
            });
            var quillEditor = document.getElementById('quil-description');
            editor.on('text-change', function() {
              if(editor.root.innerHTML == '<p><br></p>'){
                $('#description').val('');
              }else{
                $('#description').val(editor.root.innerHTML);
              }  
            });
            editor.root.innerHTML = $('#description').val();
          
        }
    });
</script>
@endsection