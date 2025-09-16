@extends('layouts.admin')

@section('title')
    Admin | Add UserGroup
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">UserGroup /</span> Create</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-body">
          

          <form action="{{ route('usergroup.store') }}" method="POST">
            @csrf
            @if(Session::has('success'))
              <div class="alert alert-success">{{ Session::get('success') }}</div>
          @endif
          @if(Session::has('error'))
              <div class="alert alert-danger">{{ Session::get('error') }}</div>
          @endif
            <div class ="col-6">
            <div class="mb-3">
              <label class="form-label" for="name">Role Name <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="name"
                  id="name"
                  value="{{ old('name') }}"
                  placeholder="Enter role name"/>
              </div>
            </div>
              @if ($errors->has('name'))
                <span class="text-danger text-left">{{ $errors->first('name') }}</span>
              @endif
            </div>

            {{-- Hidden Guard Name --}}
            <input type="hidden" name="guard_name" value="web">

            <div class="d-flex pt-3 justify-content-end">
              <a href="{{ route('usergroup.index') }}" class="btn btn-outline-primary me-2">Cancel</a>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
