@extends('layouts.admin')

@section('title')
    Admin | Event Guide Update
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
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Update/</span> Event Guide</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Event Guide Update</h5>
        </div>
        <div class="card-body">
          @if(Session::has('success'))
              <div class="alert alert-success">
                {{ Session::get('success') }}
              </div>
          @endif

          @if(Session::has('error'))
              <div class="alert alert-danger">
                {{ Session::get('error') }}
              </div>
          @endif

          <form action="{{ route('event-guides.update', $eventGuide->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="event_id">Event</label>
                  <select name="event_id" class="form-select" id="event_id">
                    @if(isSuperAdmin())
                      <option value="">Global Guides</option>
                    @endif
                    @foreach($events as $event)
                      <option value="{{ $event->id }}" {{ (string) old('event_id', $eventGuide->event_id) === (string) $event->id ? 'selected' : '' }}>
                        {{ $event->title }}
                      </option>
                    @endforeach
                  </select>
                  @error('event_id')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="category">Section Title</label>
                  <input type="text" name="category" id="category" class="form-control" value="{{ old('category', $eventGuide->category) }}" placeholder="Example: Registration 101 or Files to Download">
                  @error('category')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <!-- Title -->
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                  <input type="text" name="title" class="form-control" value="{{ old('title', $eventGuide->title) }}">
                  @error('title')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div>
              </div>

             

              <!-- Weblink -->
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="weblink">Weblink</label>
                  <input type="url" name="weblink" class="form-control" value="{{ old('weblink', $eventGuide->weblink) }}">
                  @error('weblink')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div>
              </div>
               <!-- Type -->
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="type">Description  <span class="text-danger">*</span></label>
                  <textarea name="type" class="form-control" rows="4">{{ old('type', $eventGuide->type) }}</textarea>
                 
                  @error('type')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <!-- Document Upload -->
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="doc">Document</label>
                  <input type="file" name="doc" class="form-control" accept=".pdf,.jpg,.png,.doc,.docx">
                  @if($eventGuide->documentFile?->file_path)
                    <p class="mt-2">
                      <a href="{{ $eventGuide->documentFile->file_path }}" target="_blank">
                       View Current Document
                      </a>

                    </p>
                  @endif
                  @error('doc')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="icon">Icon</label>
                  <input type="file" name="icon" id="icon" class="form-control" accept=".jpg,.jpeg,.png,.svg,.webp">
                  <div class="mt-2 {{ $eventGuide->iconFile?->file_path ? '' : 'd-none' }}" id="icon-preview-wrapper">
                    <a href="{{ $eventGuide->iconFile?->file_path ?: '#' }}" target="_blank" id="icon-preview-link">
                      <img src="{{ $eventGuide->iconFile?->file_path ?: '' }}" data-original-src="{{ $eventGuide->iconFile?->file_path ?: '' }}" alt="{{ $eventGuide->title }} icon" id="icon-preview" class="event-guide-icon-preview">
                    </a>
                  </div>
                  @error('icon')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
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
    const link = document.getElementById('icon-preview-link');

    if (!input || !preview || !wrapper || !link) {
      return;
    }

    input.addEventListener('change', function (event) {
      const file = event.target.files && event.target.files[0];

      if (!file) {
        const originalSrc = preview.getAttribute('data-original-src');

        if (originalSrc) {
          preview.src = originalSrc;
          link.href = originalSrc;
          wrapper.classList.remove('d-none');
        } else {
          preview.src = '';
          link.href = '#';
          wrapper.classList.add('d-none');
        }

        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        link.href = e.target.result;
        wrapper.classList.remove('d-none');
      };
      reader.readAsDataURL(file);
    });
  });
</script>
@endsection
