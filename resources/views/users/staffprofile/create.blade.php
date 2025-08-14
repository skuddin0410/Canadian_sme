@extends('layouts.admin')

@section('title')
    Admin | Staff Profile
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light"> Staff Profile /</span> @if(!empty($user)) Update @else Create @endif</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Staff Profile @if(!empty($user)) Update @else Create @endif</h5>
        </div>
        <div class="card-body">
          @if(Session::has('success'))
              <div class="alert alert-success">{{ Session::get('success') }}</div>
          @endif
          @if(Session::has('error'))
              <div class="alert alert-danger">{{ Session::get('error') }}</div>
          @endif
           
          <form action="@if(!empty($user)) {{ route('staff-profile.update', ['staff_profile' => $user->id]) }} @else {{ route('staff-profile.store') }} @endif" method="POST" autocomplete="off" enctype="multipart/form-data">
            @csrf
            @if(!empty($user))
              @method('PUT')
            @endif

            <div class="row"> 
              {{-- First Name --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">First Name <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      name="first_name"
                      value="{{ $user->name ?? old('first_name') }}"
                      placeholder="User first name"
                    />
                  </div>
                  @error('first_name')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              {{-- Last Name --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Last Name <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      name="last_name"
                      value="{{ $user->lastname ?? old('last_name') }}"
                      placeholder="User last name"
                    />
                  </div>
                  @error('last_name')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              {{-- Username --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">User Name <span class="text-danger">*(No spaces allowed)</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      name="username"
                      value="{{ $user->username ?? old('username') }}"
                      placeholder="User name"
                    />
                  </div>
                  @error('username')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              {{-- Email --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Email <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="email"
                      class="form-control"
                      name="email"
                      value="{{ $user->email ?? old('email') }}"
                      placeholder="User email"
                    />
                  </div>
                  @error('email')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              {{-- Mobile --}}
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label">Mobile <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      name="mobile"
                      value="{{ $user->mobile ?? old('mobile') }}"
                      placeholder="User mobile"
                    />
                  </div>
                  @error('mobile')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div>
              </div>
           
              {{-- Password --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Password @if(empty($user))<span class="text-danger">*</span>@endif</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="password"
                      class="form-control"
                      name="password"
                      autocomplete="off"
                      placeholder="Password"
                    />
                  </div>
                  @error('password')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div> 
              </div>

              {{-- Confirm Password --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Confirm Password @if(empty($user))<span class="text-danger">*</span>@endif</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="password"
                      class="form-control"
                      name="password_confirmation"
                      autocomplete="off"
                      placeholder="Confirm password"
                    />
                  </div>
                  @error('password_confirmation')
                    <span class="text-danger text-left">{{ $message }}</span>
                  @enderror
                </div> 
              </div>

              {{-- Actions --}}
              <div class="col-12">
                <div class="mb-3">
                  <div class="d-flex pt-3 justify-content-end">
                    <a href="{{ route('staff-profile.index') }}" class="btn btn-outline-primary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                  </div>
                </div>
              </div>

            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
