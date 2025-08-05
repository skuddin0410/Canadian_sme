@extends('layouts.admin')

@section('content')
<div class="container">
    <h4 class="mb-4">Company / Description</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('company.description.update') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="description" class="form-label">Company Overview / Value Proposition</label>
            <textarea name="description" id="description" class="form-control">{!! old('description', $company->description) !!}</textarea>
            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Save Description</button>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea#description',
    height: 300,
    menubar: false,
    plugins: 'link lists preview code',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link preview code'
  });
</script>
@endpush
