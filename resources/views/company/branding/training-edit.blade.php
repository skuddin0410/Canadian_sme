@extends('layouts.admin')

@section('content')
<div class="container-xxl container-p-y">
  <h4 class="mb-4"><span class="text-muted fw-light">Marketing /</span> Edit Material</h4>

  @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('trainings.update', $training->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label">Material Name</label>
          <input type="text" name="material_name" class="form-control" value="{{ $training->material_name }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">YouTube Link (optional)</label>
          <input type="url" name="youtube_link" class="form-control" value="{{ $training->youtube_link }}">
        </div>

        <div class="mb-3">
          <label class="form-label">Material Description</label>
          <textarea name="material_description" class="form-control" rows="3" required>{{ $training->material_description }}</textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Attached File</label><br>
          @if($file)
            <a href="{{ asset('storage/' . $file->file_name) }}" target="_blank">View</a><br><br>
          @endif
          <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
        </div>

        <button type="submit" class="btn btn-primary">Update Material</button>
        <a href="{{ route('trainings.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection
