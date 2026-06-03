@extends('layouts.admin')

@section('title')
    Admin | Event Guide Add
@endsection

@section('content')
<style>
.ql-editor {
     width: 100%;
}

.event-guide-icon-preview {
     max-width: 100px;
     max-height: 100px;
     object-fit: contain;
     border-radius: 8px;
     border: 1px solid #d9dee3;
     padding: 4px;
     background: #fff;
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
                  <label class="form-label">Event</label>
                  <select name="event_id" class="form-select">
                    @if(isSuperAdmin())
                      <option value="">Global Guides</option>
                    @endif
                    @foreach($events as $event)
                      <option value="{{ $event->id }}" {{ (string) old('event_id') === (string) $event->id ? 'selected' : '' }}>
                        {{ $event->title }}
                      </option>
                    @endforeach
                  </select>
                  @error('event_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Section Title</label>
                  <input type="text" name="category" class="form-control" value="{{ old('category') }}" placeholder="Example: Registration 101 or Files to Download">
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
                  <label class="form-label">Weblink</label>
                  <input type="url" name="weblink" class="form-control" value="{{ old('weblink') }}" placeholder="https://example.com">
                  @error('weblink') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>
              <div class="col-6">
                <div class="mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="type" class="form-control" rows="4">{{ old('type') }}</textarea>
                    @error('type') 
                    <span class="text-danger">{{ $message }}</span> 
                    @enderror
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Document (PDF/DOC/JPG/PNG)</label>
                  <input type="file" name="doc" class="form-control" accept=".pdf,.jpg,.png,.doc,.docx">
                  @error('doc') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Icon (JPG/PNG/SVG/WEBP)</label>
                  <input type="file" name="icon" id="icon" class="form-control" accept=".jpg,.jpeg,.png,.svg,.webp">
                  <div class="mt-2 d-none" id="icon-preview-wrapper">
                    <img src="" alt="Icon preview" id="icon-preview" class="event-guide-icon-preview">
                  </div>
                  @error('icon') <span class="text-danger">{{ $message }}</span> @enderror
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

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('icon');
    const preview = document.getElementById('icon-preview');
    const wrapper = document.getElementById('icon-preview-wrapper');

    if (!input || !preview || !wrapper) {
      return;
    }

    input.addEventListener('change', function (event) {
      const file = event.target.files && event.target.files[0];

      if (!file) {
        preview.src = '';
        wrapper.classList.add('d-none');
        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        wrapper.classList.remove('d-none');
      };
      reader.readAsDataURL(file);
    });
  });
</script>
@endsection
