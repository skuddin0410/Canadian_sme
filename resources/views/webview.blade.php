@extends('layouts.admin')

@section('title')
    Admin | Webview
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Webview/</span>Page</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Webview</h5>
        </div>
        <div class="card-body">
          <form  action="{{route('webview')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}

            <input type="hidden" name="webview" value="landing" />
            <div class="mb-3">
               <label>URL:<a href="{{config('app.url')}}" target="_blank">{{config('app.url')}}</a></label>
            </div>
            <div class="mb-3">
              <label class="form-label" for="title">Description</label>
              <div class="input-group input-group-merge" id="quill-editor">
                <textarea
                  type="text"
                  name="description"
                  id="description"
                  class="form-control"
                  placeholder="Post Description"
                  rows="8" 
                  cols="50"
                >{{ $page->description ?? old('description') }}</textarea>
              </div>
              @if ($errors->has('description'))
                <span class="text-danger text-left">{{ $errors->first('description') }}</span>
              @endif
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