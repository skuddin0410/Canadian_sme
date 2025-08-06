@extends('layouts.admin')

@section('title')
    Admin | Feature Post Add
@endsection

@section('content')

<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">CMS/</span>Page</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">CMS Page Create</h5>
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
          <form  action="{{route('pages.store')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
            <div class="col-6">
             <div class="mb-3">
              <label class="form-label" for="title">Title<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="title"
                  id="slug-source"
                  value="{{ old('title') }}"
                  placeholder="Page Title"/>
              </div>
              @if ($errors->has('title'))
                <span class="text-danger text-left">{{ $errors->first('title') }}</span>
              @endif
            </div>
          </div>
          <div class="col-6">
            <div class="mb-3">
                  <label class="form-label" for="title">slug<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      name="slug"
                      id="slug-target"
                      value="{{ old('slug') }}"
                      placeholder="Slug"
                      />
                  </div>
                  @if ($errors->has('slug'))
                    <span class="text-danger text-left">{{ $errors->first('slug') }}</span>
                  @endif
            </div>
           </div>
           </div>

            <div class="mb-3">
              <label class="form-label" for="description">Description<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge" id="quill-editor" style="height: 300px;">
                <textarea
                  type="text"
                  name="description"
                  id="description"
                  class="form-control"
                  placeholder="Post Description"
                  rows="8" cols="50"
                >{{ old('description') }}</textarea>
              </div>
              @if ($errors->has('description'))
                <span class="text-danger text-left">{{ $errors->first('description') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label" for="title">Image<span class="text-danger">*</span><span class="text-danger">(Allowed file size : {{config('app.blog_image_size')." KB and allowed file type ".config('app.image_mime_types') }}) </span> </label>
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
              <label class="form-label" for="title">Tags<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="tags"
                  id="tags"
                  value="{{ old('tags') }}"
                  placeholder="Page tags"/>
              </div>
              @if ($errors->has('tags'))
                <span class="text-danger text-left">{{ $errors->first('tags') }}</span>
              @endif
            </div>

             <div class="mb-3">
              <label class="form-label" for="meta_title">Meta title</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="meta_title"
                  id="meta_title"
                  value="{{old('meta_title')}}"
                  placeholder="Page meta title"/>
              </div>
              @if ($errors->has('meta_title'))
                <span class="text-danger text-left">{{ $errors->first('meta_title') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label" for="meta_description">Meta description</label>
              <div class="input-group input-group-merge" id="quill-editor1" style="height: 300px;">
                <textarea
                  type="text"
                   name="quil-description1"
                  id="quil-description1"
                  class="form-control"
                  placeholder="Page description"
                  rows="8" cols="50"
                >{{old('meta_description') }}</textarea>

              </div>
              @if ($errors->has('meta_description'))
                <span class="text-danger text-left">{{ $errors->first('meta_description') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label" for="meta_description">Meta keywords</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="meta_keywords"
                  id="meta_keywords"
                  value="{{ old('meta_keywords') }}"
                  placeholder="Blog meta description"/>
              </div>
              @if ($errors->has('meta_keywords'))
                <span class="text-danger text-left">{{ $errors->first('meta_keywords') }}</span>
              @endif
            </div>
            
            <div class="mb-3">
              <label class="form-label" for="title">Status<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <select class="form-control" name="status" id="status"  onchange="toggleScheduledFields()">
                    @foreach(['publish', 'draft', 'scheduled'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $e->status ?? '') === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
              </div>
              @if ($errors->has('status'))
                <span class="text-danger text-left">{{ $errors->first('status') }}</span>
              @endif
            </div>

          <div id="scheduledFields" class="mb-3">
            <div class="row">
              <div class="col-md-6">
                  <label for="start_date" class="form-label">Scheduled Start Date<span class="text-danger">*</span></label>
                  <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" class="form-control">
                @if ($errors->has('start_date'))
                <span class="text-danger text-left">{{ $errors->first('start_date') }}</span>
               @endif
              </div>
              <div class="col-md-6">
                  <label for="end_date" class="form-label">Scheduled End Date<span class="text-danger">*</span></label>
                  <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" class="form-control">

                  @if ($errors->has('end_date'))
                     <span class="text-danger text-left">{{ $errors->first('end_date') }}</span>
                  @endif
              </div>
            </div>  
          </div>

          <div class="d-flex pt-3 justify-content-end">
             <a href="{{route('pages.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
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

function slugify(str) {
  str = str.replace(/^\s+|\s+$/g, ''); // trim leading/trailing white space
  str = str.toLowerCase(); // convert string to lowercase
  str = str.replace(/[^a-z0-9 -]/g, '') // remove any non-alphanumeric characters
           .replace(/\s+/g, '-') // replace spaces with hyphens
           .replace(/-+/g, '-'); // remove consecutive hyphens
  return str.replace(/^-+|-+$/g, '');
}
</script>
<script>
    function toggleScheduledFields() {
        const visibility = document.getElementById('status').value;
        const scheduledFields = document.getElementById('scheduledFields');
        if (visibility === 'scheduled') {
            scheduledFields.classList.remove('d-none');
        } else {
            scheduledFields.classList.add('d-none');
        }
    }

    // Optional: auto-run if editing form with scheduled pre-selected
    document.addEventListener('DOMContentLoaded', function () {
        toggleScheduledFields();
    });
</script>


@endsection