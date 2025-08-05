@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Company /</span> Details
  </h4>

  @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  <div class="card mb-4">
    <div class="card-body">
      <form method="POST" action="{{ route('company.update', $company->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Two-column layout --}}
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Company Name</label>
            <input type="text" name="name" value="{{ old('name', $company->name) }}" class="form-control" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Industry</label>
            <input type="text" name="industry" value="{{ old('industry', $company->industry) }}" class="form-control">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Size</label>
            <input type="text" name="size" value="{{ old('size', $company->size) }}" class="form-control">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" value="{{ old('location', $company->location) }}" class="form-control">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $company->email) }}" class="form-control">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="form-control">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Website</label>
            <input type="url" name="website" value="{{ old('website', $company->website) }}" class="form-control">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">LinkedIn</label>
            <input type="url" name="linkedin" value="{{ old('linkedin', $company->linkedin) }}" class="form-control">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Twitter</label>
            <input type="url" name="twitter" value="{{ old('twitter', $company->twitter) }}" class="form-control">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Facebook</label>
            <input type="url" name="facebook" value="{{ old('facebook', $company->facebook) }}" class="form-control">
          </div>
        </div>

        {{-- Full-width: Description --}}
        <h5 class="mt-4 mb-2">Company Description</h5>
        <div class="mb-3">
          <textarea name="description" rows="6" class="form-control" placeholder="Describe your company...">{{ old('description', $company->description) }}</textarea>
        </div>

        {{-- Full-width: Certifications --}}
        <h5 class="mt-4 mb-2">Certifications</h5>
        <div class="mb-3">
          <textarea name="certifications" rows="4" class="form-control" placeholder="Enter certifications">{{ old('certifications', $company->certifications) }}</textarea>
        </div>
       
<div class="mb-3">
  <label for="certification_image" class="form-label">Upload Certification Image</label>
  <input type="file" name="certification_image" id="certification_image" class="form-control" accept="image/*">
  
  {{-- File name display --}}
  <small id="selected-file-name" class="form-text text-muted mt-1"></small>

  {{-- Display previously uploaded image --}}
  @if ($company->certificationFile && $company->certificationFile->file_name)
    <div class="mt-2">
      <strong>Previously Uploaded:</strong> {{ basename($company->certificationFile->file_name) }}<br>
      <img src="{{ asset('storage/' . $company->certificationFile->file_name) }}" alt="Certification Image" width="200" class="mt-2">
    </div>
  @endif
</div>
  <div class="mt-3">
          <button type="submit" class="btn btn-primary">Update</button>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('certification_image').addEventListener('change', function (e) {
    const fileName = e.target.files[0]?.name;
    document.getElementById('selected-file-name').innerText = fileName || 'No file chosen';
  });
</script>
@endpush
