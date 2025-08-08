@extends('layouts.admin')

@section('content')
<div class="container">
  <h4 class="mb-4">Booth Details</h4>

  <div class="card">
    <div class="d-flex pt-3 justify-content-end">
    <a href="{{ route('booths.index') }}" class="btn btn-outline-primary me-2">Back</a>
    </div>
    <div class="card-body">
      <p><strong>Title:</strong> {{ $booth->title }}</p>
      <p><strong>Booth Number:</strong> {{ $booth->booth_number }}</p>
      <p><strong>Size:</strong> {{ $booth->size }}</p>
      <p><strong>Company:</strong> {{ $booth->company->name ?? 'N/A' }}</p>
      <p><strong>Location Pref:</strong> {{ $booth->location_preferences }}</p>
      <p><strong>Created:</strong> {{ $booth->created_at->format('d M Y') }}</p>
    </div>
  </div>
</div>
@endsection
