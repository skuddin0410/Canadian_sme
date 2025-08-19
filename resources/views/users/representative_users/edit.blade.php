@extends('layouts.admin')

@section('title')
    Admin | Edit Exhibitor Representative Data
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Exhibitor Representative /</span> Edit</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Edit Exhibitor Representative</h5>
        </div>
        <div class="card-body">
          @if(Session::has('success'))
              <div class="alert alert-success">{{ Session::get('success') }}</div>
          @endif
          @if(Session::has('error'))
              <div class="alert alert-danger">{{ Session::get('error') }}</div>
          @endif

          <form 
            action="{{ route('representative-users.update', $user->id) }}" 
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
                  <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $user->name) }}" placeholder="User first name">
                  @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Last Name --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Last Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $user->lastname) }}" placeholder="User last name">
                  @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Username --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Username <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="username" value="{{ old('username', $user->username) }}" placeholder="Username">
                  @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Email --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Email <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" placeholder="Email">
                  @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>
              <!-- Designation -->
<div class="col-6">
    <div class="mb-3">
        <label class="form-label" for="designation">Designation</label>
        <div class="input-group input-group-merge">
            <span id="designation-icon" class="input-group-text">
                <i class="bx bx-briefcase"></i>
            </span>
            <input
                type="text"
                class="form-control"
                name="designation"
                id="designation"
                value="{{ old('designation', $user->designation ?? '') }}"
                placeholder="Enter designation"/>
        </div>
        @if ($errors->has('designation'))
            <span class="text-danger text-left">{{ $errors->first('designation') }}</span>
        @endif
    </div>
</div>

<!-- Tags -->
<div class="col-6">
    <div class="mb-3">
        <label class="form-label" for="tags">Tags</label>
        <div class="input-group input-group-merge">
            <span id="tags-icon" class="input-group-text">
                <i class="bx bx-purchase-tag"></i>
            </span>
            <input
                type="text"
                class="form-control"
                name="tags"
                id="tags"
                value="{{ old('tags', $user->tags ?? '') }}"
                data-role="tagsinput"
                placeholder="Add tags (comma separated)" />
        </div>
        @if ($errors->has('tags'))
            <span class="text-danger text-left">{{ $errors->first('tags') }}</span>
        @endif
    </div>
</div>


              {{-- Website --}}
<div class="col-6">
  <div class="mb-3">
    <label class="form-label">Website</label>
    <input type="url" class="form-control" name="website_url" value="{{ old('website_url', $user->website_url) }}" placeholder="https://example.com">
    @error('website_url') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

{{-- LinkedIn --}}
<div class="col-6">
  <div class="mb-3">
    <label class="form-label">LinkedIn</label>
    <input type="url" class="form-control" name="linkedin_url" value="{{ old('linkedin_url', $user->linkedin_url) }}" placeholder="https://linkedin.com/in/username">
    @error('linkedin_url') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>


              {{-- Mobile --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Mobile <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="mobile" value="{{ old('mobile', $user->mobile) }}" placeholder="Mobile">
                  @error('mobile') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- DOB --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" name="dob" value="{{ old('dob', $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '') }}">
                  @error('dob') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Gender --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Gender</label>
                  <select class="form-control" name="gender">
                    <option value="">Select</option>
                    <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
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
                  <input type="text" class="form-control" name="{{ $field }}" value="{{ old($field, $user->$field) }}" placeholder="{{ ucfirst($field) }}">
                  @error($field) <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>
              @endforeach

              {{-- Website & LinkedIn --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Website</label>
                  <input type="url" class="form-control" name="website_url" value="{{ old('website_url', $user->website_url) }}" placeholder="Website URL">
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">LinkedIn</label>
                  <input type="url" class="form-control" name="linkedin_url" value="{{ old('linkedin_url', $user->linkedin_url) }}" placeholder="LinkedIn URL">
                </div>
              </div>

              {{-- Role --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Role <span class="text-danger">*</span></label>
                  <select class="form-control" name="user_type" required>
                   
                    <option value="Exhibitor Representative" {{ old('user_type', $user->roles[0]->name ?? '') == 'Exhibitor Representative' ? 'selected' : '' }}>Exhibitor Representative</option>
                  </select>
                </div>
              </div>

              {{-- Password --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Password <small class="text-muted">(leave blank to keep unchanged)</small></label>
                  <input type="password" class="form-control" name="password" placeholder="New Password">
                  @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              
            
              {{-- Submit --}}
              <div class="col-12">
                <div class="d-flex justify-content-end pt-3">
                  <a href="{{ route('representative-users.index') }}" class="btn btn-outline-primary me-2">Cancel</a>
                  <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i>Save</button>
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
