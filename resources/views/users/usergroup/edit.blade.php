@extends('layouts.admin')

@section('title')
Admin | Edit UserGroup
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">UserGroup /</span> Edit</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Edit Role</h5>
        </div>
        <div class="card-body">

          {{-- Validation Errors --}}
          @if ($errors->any())
              <div class="alert alert-danger">
                  <ul class="mb-0">
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif

          {{-- Form --}}
          <form action="{{ route('usergroup.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label" for="name">Role Name <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-shield"></i></span>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        name="name" 
                        id="name"
                        value="{{ old('name', $role->name) }}" 
                        placeholder="Enter role name" 
                        required>
                  </div>
                  @error('name')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-end pt-3">
              <a href="{{ route('usergroup.index') }}" class="btn btn-outline-primary me-2">
                <i class="bx bx-arrow-back"></i> Cancel
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-save"></i> Update
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
