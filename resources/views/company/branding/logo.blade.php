@extends('layouts.admin')

@section('content')
<div class="container">
  <h4 class="py-3 mb-4">Company Branding / Logo Upload</h4>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('company.branding.logo.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- <div class="mb-3">
      <label for="logo" class="form-label">Upload Logo (PNG, JPG, SVG, PDF)</label>
      <input type="file" name="logo" id="logo" class="form-control" accept=".png,.jpg,.jpeg,.svg,.pdf" required>

      @if($logo)
        <div class="mt-3">
          <strong>Previously Uploaded:</strong> {{ basename($logo->file_name) }}<br>
          @if(Str::endsWith($logo->file_name, ['.jpg', '.jpeg', '.png', '.svg']))
            <img src="{{ asset('storage/' . $logo->file_name) }}" alt="Company Logo" style="max-width: 200px;">
          @else
            <a href="{{ asset('storage/' . $logo->file_name) }}" target="_blank">View PDF</a>
          @endif
        </div>
      @endif
    </div> --}}
    <div class="mb-3">
  <label for="logo" class="form-label">Upload Company Logo</label>
  <input type="file" name="logo" id="logo" class="form-control" accept="image/*">

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


    <button type="submit" class="btn btn-primary">Upload Logo</button>
  </form>
</div>
@endsection
