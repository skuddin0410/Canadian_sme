@extends('layouts.admin')

@section('content')
<div class="container">
  <h4 class="mb-4">Edit Booth</h4>

  @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  <form action="{{ route('booths.update', $booth->id) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- <div class="mb-3">
      <label>Company</label>
      <select name="company_id" class="form-control" required>
        @foreach($companies as $company)
          <option value="{{ $company->id }}" {{ $booth->company_id == $company->id ? 'selected' : '' }}>
            {{ $company->name }}
          </option>
        @endforeach
      </select>
    </div> --}}

    <div class="mb-3">
      <label>Booth Title</label>
      <input type="text" name="title" class="form-control" value="{{ $booth->title }}">
    </div>

    <div class="mb-3">
      <label>Booth Number</label>
      <input type="text" name="booth_number" class="form-control" value="{{ $booth->booth_number }}" required>
    </div>

    <div class="mb-3">
      <label>Size</label>
      <input type="text" name="size" class="form-control" value="{{ $booth->size }}">
    </div>

    <div class="mb-3">
      <label>Location Preferences</label>
      <textarea name="location_preferences" class="form-control" rows="3">{{ $booth->location_preferences }}</textarea>
    </div>

    <button class="btn btn-primary">Update Booth</button>
    <a href="{{ route('booths.index') }}" class="btn btn-secondary">Cancel</a>
  </form>
</div>
@endsection
