@extends('layouts.admin') @section('content') <div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Company /</span> Details
  </h4> @if(Session::has('success')) <div class="alert alert-success">{{ Session::get('success') }}</div> @endif <div class="card mb-4">
    <div class="card-body"> 

      @if(!empty($company)) <form method="POST" action="{{ route('company.update', $company->id) }}" enctype="multipart/form-data"> 
          @method('PUT')
        @else 
        <form method="POST" action="{{ route('company.store') }}" enctype="multipart/form-data">

         @endif
          @csrf 
          <div class="row">

            <div class="col-md-6 mb-3">
              <label class="form-label">Company Name <span class="text-danger">*</span></label>
              <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}" class="form-control" required>
            </div>

          <div class="mb-3">
            <label for="logo" class="form-label">Upload Company Logo<span class="text-danger">*</span></label>
            <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
            
            <small id="selected-file-name" class="form-text text-muted mt-1"></small>
             @if ($company && $company->logoFile && $company->logoFile->file_name) <div class="mt-2">
              <img src="{{ asset($company->logoFile->file_path) }}" alt="Certification Image" width="200" class="mt-2">
            </div> @endif
          </div>
      
            <div class="col-md-6 mb-3">
              <label class="form-label">Industry <span class="text-danger">*</span></label>
              <input type="text" name="industry" value="{{ old('industry', $company->industry ?? '') }}" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Size <span class="text-danger">*</span></label>
              <input type="text" name="size" value="{{ old('size', $company->size ?? '') }}" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Location <span class="text-danger">*</span></label>
              <input type="text" name="location" value="{{ old('location', $company->location ?? '') }}" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email<span class="text-danger">*</span></label>
              <input type="email" name="email" value="{{ old('email', $company->email ?? '') }}" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Phone <span class="text-danger">*</span></label>
              <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Website <span class="text-danger">*</span></label>
              <input type="url" name="website" value="{{ old('website', $company->website ?? '') }}" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">LinkedIn <span class="text-danger">*</span></label>
              <input type="url" name="linkedin" value="{{ old('linkedin', $company->linkedin ?? '') }}" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Twitter<span class="text-danger">*</span></label>
              <input type="url" name="twitter" value="{{ old('twitter', $company->twitter ?? '') }}" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Facebook<span class="text-danger">*</span></label>
              <input type="url" name="facebook" value="{{ old('facebook', $company->facebook ?? '') }}" class="form-control">
            </div>

          </div>
        
          <h5 class="mt-4 mb-2">Company Description<span class="text-danger">*</span></h5>
          <div class="mb-3">
            <textarea name="description" id="description" rows="6" class="form-control" placeholder="Describe your company...">{{ old('description', $company->description ?? '') }}</textarea>
          </div>
          

          <h5 class="mt-4 mb-2">Certifications<span class="text-danger">*</span></h5>
          <div class="mb-3">
            <textarea name="certifications" rows="4" class="form-control" placeholder="Enter certifications">{{ old('certifications', $company->certifications ?? '') }}</textarea>
          </div>

          <div class="mb-3">
            <label for="certification_image" class="form-label">Upload Certification Image</label>
            <input type="file" name="certification_image" id="certification_image" class="form-control" accept="image/*">
            
            <small id="selected-file-name" class="form-text text-muted mt-1"></small>
             @if ($company && $company->certificationFile && $company->certificationFile->file_name) <div class="mt-2">
              <img src="{{ asset($company->certificationFile->file_path) }}" alt="Certification Image" width="200" class="mt-2">
            </div> @endif
          </div>
          <div class="mt-3">
            <div class="d-flex pt-3 justify-content-end">
            <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user">{{!empty($company) ? 'Update' : 'Save'}}</button>
          </div>
          </div>
        </form>
    </div>
  </div>
</div> 
@endsection 

@push('scripts')
 <script>
  document.getElementById('certification_image').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    document.getElementById('selected-file-name').innerText = fileName || 'No file chosen';
  });
</script> 
@endpush