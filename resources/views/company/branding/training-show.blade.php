@extends('layouts.admin')

@section('content')
<div class="container-xxl container-p-y">
  <h4 class="mb-4"><span class="text-muted fw-light">Marketing /</span> View Material</h4>

  <div class="card">
    <div class="card-body">
      <h5>{{ $training->material_name }}</h5>

      <p class="mb-3">{{ $training->material_description }}</p>

      @if ($training->youtube_link)
        <p><strong>YouTube:</strong> <a href="{{ $training->youtube_link }}" target="_blank">{{ $training->youtube_link }}</a></p>
      @endif

      @if ($file)
        <p><strong>File:</strong> 
          <a href="{{ asset('storage/' . $file->file_name) }}" target="_blank">View file</a>
        </p>
      @endif

      <p><small><strong>Uploaded on:</strong> {{ $training->created_at->format('d M Y') }}</small></p>

      <a href="{{ route('trainings.index') }}" class="btn btn-secondary mt-3">Back to List</a>
    </div>
  </div>
</div>
@endsection
