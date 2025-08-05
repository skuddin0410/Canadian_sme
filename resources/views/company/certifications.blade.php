@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">Company / Certifications</h4>

  @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  <form method="POST" action="{{ route('company.certifications.update') }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label for="certifications" class="form-label">Certifications & Compliance Info</label>
      <textarea name="certifications" id="certifications" class="form-control" rows="5" placeholder="Enter industry certifications, ISO compliance, etc.">{{ old('certifications', $company->certifications) }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
  </form>
</div>
@endsection
