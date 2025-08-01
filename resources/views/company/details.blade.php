@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Company /</span> Details</h4>

  <div class="card mb-4">
    <div class="card-body">
      @if(Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
      @endif

      <form method="POST" action="{{ route('company.update', $company->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label">Company Name</label>
          <input type="text" name="name" value="{{ old('name', $company->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Industry</label>
          <input type="text" name="industry" value="{{ old('industry', $company->industry) }}" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Size</label>
          <input type="text" name="size" value="{{ old('size', $company->size) }}" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Location</label>
          <input type="text" name="location" value="{{ old('location', $company->location) }}" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" value="{{ old('email', $company->email) }}" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="form-control">
        </div>

        {{-- <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4">{{ old('description', $company->description) }}</textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Website</label>
          <input type="url" name="website" value="{{ old('website', $company->website) }}" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">LinkedIn</label>
          <input type="url" name="linkedin" value="{{ old('linkedin', $company->linkedin) }}" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Twitter</label>
          <input type="url" name="twitter" value="{{ old('twitter', $company->twitter) }}" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Facebook</label>
          <input type="url" name="facebook" value="{{ old('facebook', $company->facebook) }}" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Certifications</label>
          <textarea name="certifications" class="form-control" rows="2">{{ old('certifications', $company->certifications) }}</textarea>
        </div> --}}

        <button type="submit" class="btn btn-primary">Update Company Info</button>
        <a href="{{ route('company.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection
