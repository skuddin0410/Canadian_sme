@extends('layouts.admin')

@section('content')
<div class="container">
  <h4 class="py-3 mb-4">Branding & Media / Logo Upload</h4>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('company.branding.logo.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row align-items-center mb-3">
      <div class="col-md-6">
  <label for="logo" class="form-label">Upload Company Logo</label>
  <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
  </div>

  <div class="col-md-3 mt-4">
   <button type="submit" class="btn btn-primary">Upload</button>
  </div>
</div>

  {{-- Display Uploaded Logo --}}
  @if ($company->logoFile && $company->logoFile->file_name)
    <div class="mt-3">
      @php
        $filePath = $company->logoFile->file_name;
        $fileName = basename($filePath);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
      @endphp

      <strong>Uploaded File:</strong> {{ $fileName }}<br>

      @if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'svg']))
        <img src="{{ asset('storage/' . $filePath) }}" alt="Company Logo" style="max-width: 200px;" class="mt-2">
      @elseif($fileExt === 'pdf')
        <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2">View PDF Logo</a>
      @endif
    </div>
  @endif
</div>


   
  </form>
</div>
@endsection
