@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">Company / Website Integration</h4>

  @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  <form method="POST" action="{{ route('company.websites.update') }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label for="website" class="form-label">Website</label>
      <input type="url" class="form-control" name="website" value="{{ old('website', $company->website) }}">
    </div>

    <div class="mb-3">
      <label for="linkedin" class="form-label">LinkedIn</label>
      <input type="url" class="form-control" name="linkedin" value="{{ old('linkedin', $company->linkedin) }}">
    </div>

    <div class="mb-3">
      <label for="twitter" class="form-label">Twitter</label>
      <input type="url" class="form-control" name="twitter" value="{{ old('twitter', $company->twitter) }}">
    </div>

    <div class="mb-3">
      <label for="facebook" class="form-label">Facebook</label>
      <input type="url" class="form-control" name="facebook" value="{{ old('facebook', $company->facebook) }}">
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
  </form>
</div>
@endsection
