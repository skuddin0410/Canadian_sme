@extends('layouts.admin')

@section('title')
    Admin | Event Guide Add
@endsection

@section('content')
<style>
.ql-editor {
     width: 100%;
}
</style>
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Add/</span>Event Guide</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Event Guide Create</h5>
        </div>
        <div class="card-body">
          @if(Session::has('success'))
              <div class="alert alert-success">{{ Session::get('success') }}</div>
          @endif

          @if(Session::has('error'))
              <div class="alert alert-danger">{{ Session::get('error') }}</div>
          @endif

          <form action="{{ route('event-guides.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
              
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Category<span class="text-danger">*</span></label>
                  <input type="text" name="category" class="form-control" value="{{ old('category') }}">
                  @error('category') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Title<span class="text-danger">*</span></label>
                  <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                  @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Type<span class="text-danger">*</span></label>
                  <input type="text" name="type" class="form-control" value="{{ old('type') }}">
                  @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Weblink</label>
                  <input type="url" name="weblink" class="form-control" value="{{ old('weblink') }}" placeholder="https://example.com">
                  @error('weblink') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label">Document (PDF/DOC)</label>
                  <input type="file" name="doc" class="form-control" accept=".pdf,.doc,.docx">
                  @error('doc') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

            </div>

            <div class="d-flex pt-3 justify-content-end">
              <a href="{{ route('event-guides.index') }}" class="btn btn-outline-primary me-2">Cancel</a>
              <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Save</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
