@extends('layouts.admin')

@section('content')
<div class="container">
  
  <h4 class="mb-4">Add Booth</h4>
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('booths.store') }}" method="POST">
    @csrf
   

    <div class="mb-3">
      <label>Booth Title</label>
      <input type="text" name="title" class="form-control">
    </div>

    <div class="mb-3">
      <label>Booth Number</label>
      <input type="text" name="booth_number" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Size</label>
      <input type="text" name="size" class="form-control" placeholder="e.g. 10x10">
    </div>

    <div class="mb-3">
      <label>Location Preferences</label>
      <textarea name="location_preferences" class="form-control" rows="3"></textarea>
    </div>

    <button class="btn btn-primary">Save</button>
     <a href="{{ route('booths.index') }}" class="btn btn-secondary">Cancel</a>
  </form>
</div>
@endsection
