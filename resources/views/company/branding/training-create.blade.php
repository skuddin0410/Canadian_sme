@extends('layouts.admin')

@section('content')
<div class="container-xxl container-p-y">
  <h4 class="mb-4"><span class="text-muted fw-light">Marketing /</span> Add Material</h4>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="card-header">New Material Upload</div>
    <div class="card-body">
      <form method="POST" action="{{ route('trainings.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
          <div class="mb-3 col-md-6">
            <label class="form-label">Material Name</label>
            <input type="text" name="material_name" value="{{old('material_name')}}" class="form-control" required>
          </div>
          <div class="mb-3 col-md-6">
            <label class="form-label">YouTube Link (optional)</label>
            <input type="url" name="youtube_link" value="{{old('youtube_link')}}" class="form-control">
          </div>
          <div class="mb-3 col-12">
            <label class="form-label">Material Description</label>
            <textarea name="material_description" id="description" class="form-control" rows="3" required>
              {{old('material_description')}}
            </textarea>
          </div>
          <div class="mb-3 col-md-6">
            <label class="form-label">Attach File (PDF/Image/Doc)</label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
          </div>
        </div>
        <div class="d-flex pt-3 justify-content-end">
             <a href="{{route('trainings.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user">Save</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection
