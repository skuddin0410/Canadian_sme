@extends('layouts.admin')

@section('title')
    Admin | Exhibitor User Edit
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Exhibitor User /</span> Edit</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Edit Exhibitor User</h5>
        </div>
        <div class="card-body">

          @if(Session::has('success'))
              <div class="alert alert-success">{{ Session::get('success') }}</div>
          @endif
          @if(Session::has('error'))
              <div class="alert alert-danger">{{ Session::get('error') }}</div>
          @endif

          @if (session('status'))
      <div class="alert alert-success">
          {{ session('status') }}
      </div>
  @endif

          {{-- Main Update Form --}}
          <form 
            action="{{ route('exhibitor-users.update', $exhibitor_user->id) }}" 
            method="POST" 
            enctype="multipart/form-data"
          >
            @csrf
            @method('PUT')

            <div class="row">
              {{-- First Name --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">First Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $exhibitor_user->name) }}" placeholder="User first name">
                  @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Last Name --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Last Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $exhibitor_user->lastname) }}" placeholder="User last name">
                  @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Username --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Username <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="username" value="{{ old('username', $exhibitor_user->username) }}" placeholder="Username">
                  @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Email --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Email <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" name="email" value="{{ old('email', $exhibitor_user->email) }}" placeholder="Email">
                  @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Mobile --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Mobile <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="mobile" value="{{ old('mobile', $exhibitor_user->mobile) }}" placeholder="Mobile">
                  @error('mobile') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- DOB --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" name="dob" value="{{ old('dob', $exhibitor_user->dob ? \Carbon\Carbon::parse($exhibitor_user->dob)->format('Y-m-d') : '') }}">
                  @error('dob') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Gender --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Gender</label>
                  <select class="form-control" name="gender">
                    <option value="male" {{ old('gender', $exhibitor_user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $exhibitor_user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                  </select>
                </div>
              </div>

              {{-- Address Fields --}}
              @php
                $fields = ['place', 'street', 'zipcode', 'city', 'state', 'country'];
              @endphp
              @foreach($fields as $field)
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">{{ ucfirst($field) }}</label>
                  <input type="text" class="form-control" name="{{ $field }}" value="{{ old($field, $exhibitor_user->$field) }}" placeholder="{{ ucfirst($field) }}">
                  @error($field) <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>
              @endforeach

              {{-- Role --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Role</label>
                  <select class="form-control" name="user_type">
                    <option value="">Select Role</option>
                    <option value="Admin" {{ old('user_type', $exhibitor_user->roles[0]->name ?? '') == 'Admin' ? 'selected' : '' }}>Admin</option>
                  </select>
                </div>
              </div>

              {{-- Company Details --}}
              <div class="col-6">
                  <div class="mb-3">
                      <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                      <input type="text" name="company_name" id="company_name" 
                             class="form-control" 
                             value="{{ old('company_name', optional($exhibitor_user->company)->name) }}" required>
                  </div>
              </div>

              <div class="col-6">
                  <div class="mb-3">
                      <label for="company_email" class="form-label">Company Email <span class="text-danger">*</span></label>
                      <input type="email" name="company_email" id="company_email" 
                             class="form-control" 
                             value="{{ old('company_email', optional($exhibitor_user->company)->email) }}" required>
                  </div>
              </div>

              <div class="col-6">
                  <div class="mb-3">
                      <label for="company_phone" class="form-label">Company Phone <span class="text-danger">*</span></label>
                      <input type="text" name="company_phone" id="company_phone" 
                             class="form-control" 
                             value="{{ old('company_phone', optional($exhibitor_user->company)->phone) }}" required>
                  </div>
              </div>

              <div class="col-12">
                <div class="mb-3">
                  <label for="company_description" class="form-label">Description</label>
                  <textarea name="company_description" id="company_description" 
                            class="form-control">{{ old('company_description', optional($exhibitor_user->company)->description) }}</textarea>
                </div>
              </div>
              </form>

              {{-- Buttons Row --}}
            <div class="col-12">
  <div class="d-flex justify-content-end pt-3 gap-2">
    <!-- Cancel -->
    <a href="{{ route('exhibitor-users.index') }}" class="btn btn-outline-primary px-4 py-2">
      Cancel
    </a>

    <!-- Reset Password Form -->
    <form action="{{ route('password.email') }}" method="POST" class="m-0">
      @csrf
      <input type="hidden" name="email" value="{{ $exhibitor_user->email }}">
      <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center gap-1">
        <i class="bx bx-key"></i> Reset Password
      </button>
    </form>

    <!-- Save Button (belongs to main form) -->
    <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center gap-1">
      <i class="bx bx-save"></i> Save
    </button>
  </div>
</div>

          

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
