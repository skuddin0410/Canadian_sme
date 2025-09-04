@extends('layouts.admin')

@section('title')
    Admin | Categories
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Categories Or Tags</span></h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Category Or Tag @if(!empty($category)) Update @else Create @endif</h5>
        </div>
        <div class="card-body">
          @if(!empty($category))
             <form  action="{{route('categories.update',["category"=>$category->id])}}" method="POST" enctype="multipart/form-data">
          @else
             <form  action="{{route('categories.store')}}" method="POST" enctype="multipart/form-data">
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
                  id="slug-source"
                  value="{{ $category->name ?? old('name') }}"
                  placeholder="Name"/>
              </div>
              @if ($errors->has('name'))
                <span class="text-danger text-left">{{ $errors->first('name') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label" for="title">Slug<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="slug"
                  id="slug-target"
                  value="{{ $category->slug ?? old('slug') }}"
                  placeholder="slug"/>
              </div>
              @if ($errors->has('slug'))
                <span class="text-danger text-left">{{ $errors->first('slug') }}</span>
              @endif
            </div>
            
            
            <div class="mb-3">
            <label for="type" class="form-label">Category Type</label>
            <select name="type" id="type" 
                    class="form-select @error('type') is-invalid @enderror" required>
                <option value="">-- Select Type --</option>
                <option value="events" {{ old('type', $category->type ?? '') == 'events' ? 'selected' : '' }}>Events</option>
                <option value="tags" {{ old('type', $category->type ?? '') == 'tags' ? 'selected' : '' }}>Tags</option>

                <option value="sponsor" {{ old('type', $category->type ?? '') == 'sponsor' ? 'selected' : '' }}>Sponsor</option>
            </select>
            @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

          <div class="mb-3" id="extraSection" style="display:none; margin-top:10px; padding:10px; border:1px solid #ccc;">
            <label for="color" class="form-label">Theme Color</label>
            <input type="color" class="form-control form-control-color" 
                   id="color" name="color" value="{{$category->color ?? '#0d6efd'}}" title="Choose your color">
          </div>


            @if(!empty($category))
             @method('PUT')
            @endif
            <div class="d-flex pt-3 justify-content-end">
            <a href="{{route('categories.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>  
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
  let section = document.getElementById('extraSection');
  @if(!empty($category) && $category->type == 'sponsor')
    section.style.display = 'block';
  @endif

  document.getElementById('type').addEventListener('change', function() {
    
      if (this.value === 'sponsor') {
        section.style.display = 'block';
      } else {
        section.style.display = 'none';
      }
  });
</script>
@endsection