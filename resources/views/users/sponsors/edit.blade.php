@extends('layouts.admin')

@section('title')
Admin | Edit Sponsors Data
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sponsors /</span> Edit</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Edit Sponsors</h5>
        </div>
        <div class="card-body">
          @if(Session::has('success'))
          <div class="alert alert-success">{{ Session::get('success') }}</div>
          @endif
          @if(Session::has('error'))
          <div class="alert alert-danger">{{ Session::get('error') }}</div>
          @endif

          <form action="{{ route('sponsors.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
              {{-- First Name --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">First Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $user->name) }}"
                    placeholder="User first name">
                  @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Last Name --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Last Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="last_name"
                    value="{{ old('last_name', $user->lastname) }}" placeholder="User last name">
                  @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>
             
               <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Mobile <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="mobile" value="{{ old('mobile', $user->mobile) }}"
                    placeholder="Mobile">
                  @error('mobile') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Email --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Email <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}"
                    placeholder="Email">
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
                    <input type="text" class="form-control" name="designation" id="designation"
                      value="{{ old('designation', $user->designation ?? '') }}" placeholder="Enter designation" />
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
                    <input type="text" class="form-control" name="tags" id="tags"
                      value="{{ old('tags', $user->tags ?? '') }}" data-role="tagsinput"
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
                  <input type="url" class="form-control" name="website_url"
                    value="{{ old('website_url', $user->website_url) }}" placeholder="https://example.com">
                  @error('website_url') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- LinkedIn --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">LinkedIn</label>
                  <input type="url" class="form-control" name="linkedin_url"
                    value="{{ old('linkedin_url', $user->linkedin_url) }}"
                    placeholder="https://linkedin.com/in/username">
                  @error('linkedin_url') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>
               <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="facebook_url">Facebook</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-facebook"></i></span>
                    <input type="url" class="form-control" name="facebook_url" id="facebook_url"
                      value="{{ old('facebook_url' , $user->facebook_url) }}" placeholder="https://facebook.com" />
                  </div>
                  @if ($errors->has('facebook_url'))
                  <span class="text-danger">{{ $errors->first('facebook_url') }}</span>
                  @endif
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="instagram">Instagram</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-instagram"></i></span>
                    <input type="url" class="form-control" name="instagram_url" id="instagram_url"
                      value="{{ old('instagram_url',$user->instagram_url) }}" placeholder="https://instagram.com" />
                  </div>
                  @if ($errors->has('instagram_url'))
                  <span class="text-danger">{{ $errors->first('instagram_url') }}</span>
                  @endif
                </div>
              </div>
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label" for="twitter">Twitter</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-twitter"></i></span>
                    <input type="url" class="form-control" name="twitter_url" id="twitter_url"
                      value="{{ old('twitter_url',$user->twitter_url) }}" placeholder="https://twitter.com" />
                  </div>
                  @if ($errors->has('twitter_url'))
                  <span class="text-danger">{{ $errors->first('twitter_url') }}</span>
                  @endif
                </div>
              </div>

              <input type="hidden" name="user_type" value="Sponsors">
              

              {{-- Submit --}}
              <div class="col-12">
                <div class="d-flex justify-content-end pt-3">
                  <a href="{{ route('sponsors.index') }}" class="btn btn-outline-primary me-2">Cancel</a>
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