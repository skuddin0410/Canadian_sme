@extends('layouts.admin')

@section('title')
    Admin | Event Guide Update
@endsection

@section('content')
<style>
  .ql-editor {
    width: 100%;
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
                  @if($eventGuide->doc)
                    <p class="mt-2">
                      <a href="{{ asset('storage/event_guides/'.$eventGuide->doc) }}" target="_blank">
                       View Current Document
                      </a>

                    </p>
                  @endif
                  @error('doc')
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
