@extends('layouts.admin')

@section('title')
    Admin |  Admin user
@endsection

@section('content')
@php
  $adminFormUser = $user ?? null;
@endphp
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light"> Admin user/</span>@if(!empty($adminFormUser)) Update @else Create @endif</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"> Admin user @if(!empty($adminFormUser)) Update @else Create @endif</h5>
        </div>
        <div class="card-body">
            <form id="admin_user_frm" action="@if(!empty($adminFormUser)) {{ route('admin-users.update',['admin_user'=>$adminFormUser->id]) }} @else {{ route('admin-users.store') }} @endif " method="POST" autocomplete="off" enctype="multipart/form-data" >
            {{ csrf_field() }}
             @if(!empty($adminFormUser))
             @method('PUT')
            @endif
            <div class="row"> 

            <div class="text-left">
              <input type="file" id="profileImageInput" name="image" accept="image/*" class="d-none">
              <label for="profileImageInput">
                <img id="profileImagePreview" 
                     src="{{ ($adminFormUser && $adminFormUser->photo) ? $adminFormUser->photo->file_path : '' }}" 
                     class="rounded-circle border border-2" 
                     style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;">
              </label>
              <p class="mt-2 text-muted">Click image to upload</p>
              @if ($errors->has('image'))
              <span class="text-danger text-left">{{ $errors->first('image') }}</span>
              @endif
            </div>
             
            <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="first_name">First Name<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input type="text" class="form-control" name="first_name" id="first_name"
                      value="{{ old('first_name', $adminFormUser->name ?? '') }}" placeholder="User first name" required minlength="2" maxlength="100" />
                  </div>
                  @if ($errors->has('first_name'))
                  <span class="text-danger text-left">{{ $errors->first('first_name') }}</span>
                  @endif
                </div>
              </div>
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="last_name">last name<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input type="text" class="form-control" name="last_name" id="last_name"
                      value="{{ old('last_name', $adminFormUser->lastname ?? '') }}" placeholder="User last name" required maxlength="100" />
                  </div>
                  @if ($errors->has('last_name'))
                  <span class="text-danger text-left">{{ $errors->first('last_name') }}</span>
                  @endif
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="email">Email<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input type="email" class="form-control" name="email" id="email"
                      value="{{ old('email', $adminFormUser->email ?? '') }}" placeholder="User email" required maxlength="255" />
                  </div>
                  @if ($errors->has('email'))
                  <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                  @endif
                </div>
              </div>

               <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="mobile">Mobile<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input type="text" class="form-control" name="mobile" id="mobile"
                      value="{{ old('mobile', $adminFormUser->mobile ?? '') }}" placeholder="10 digit mobile number" required maxlength="10" inputmode="numeric" />
                  </div>
                  <span class="text-danger text-left" id="mobile_error"></span>
                  @if ($errors->has('mobile'))
                  <span class="text-danger text-left">{{ $errors->first('mobile') }}</span>
                  @endif
                </div>
              </div>
              

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="designation">Designation</label>
                  <div class="input-group input-group-merge">
                    <span id="designation-icon" class="input-group-text">
                      <i class="bx bx-briefcase"></i>
                    </span>
                    <input type="text" class="form-control" name="designation" id="designation"
                      value="{{ old('designation', $adminFormUser->designation ?? '') }}" placeholder="Enter designation" maxlength="150" />
                  </div>
                  @if ($errors->has('designation'))
                  <span class="text-danger text-left">{{ $errors->first('designation') }}</span>
                  @endif
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="tags">Tags</label>
                  <div class="input-group input-group-merge">
                    <span id="tags-icon" class="input-group-text">
                      <i class="bx bx-purchase-tag"></i>
                    </span>
                    <input type="text" class="form-control" name="tags" id="tags"
                      value="{{ old('tags', $adminFormUser->tags ?? '') }}" data-role="tagsinput"
                      placeholder="Add tags (comma separated)" maxlength="255" />
                  </div>
                  @if ($errors->has('tags'))
                  <span class="text-danger text-left">{{ $errors->first('tags') }}</span>
                  @endif
                </div>
              </div>

            <div class="col-12">
              <div class="mb-3">
                 <label class="form-label">Bio <span class="text-danger">*</span></label>
                  <textarea name="bio" id="bio" class="form-control" placeholder="Speaker Bio" rows="8" required maxlength="2000">{{ old('bio', $adminFormUser->bio ?? '') }}</textarea>
                  @if ($errors->has('bio'))
                      <span class="text-danger">{{ $errors->first('bio') }}</span>
                  @endif

              </div>
            </div>

              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label" for="website_url">Website</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-link"></i></span>
                    <input type="url" class="form-control" name="website_url" id="website_url"
                      value="{{ old('website_url', $adminFormUser->website_url ?? '') }}" placeholder="https://example.com" />
                  </div>
                  <span class="text-danger" id="website_url_error"></span>
                  @if ($errors->has('website_url'))
                  <span class="text-danger">{{ $errors->first('website_url') }}</span>
                  @endif
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="linkedin_url">LinkedIn</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-linkedin"></i></span>
                    <input type="url" class="form-control" name="linkedin_url" id="linkedin_url"
                      value="{{ old('linkedin_url', $adminFormUser->linkedin_url ?? '') }}" placeholder="https://linkedin.com/in/username" />
                  </div>
                  <span class="text-danger" id="linkedin_url_error"></span>
                  @if ($errors->has('linkedin_url'))
                  <span class="text-danger">{{ $errors->first('linkedin_url') }}</span>
                  @endif
                </div>
              </div>
               <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="facebook_url">Facebook</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-facebook"></i></span>
                    <input type="url" class="form-control" name="facebook_url" id="facebook_url"
                      value="{{ old('facebook_url', $adminFormUser->facebook_url ?? '') }}" placeholder="https://facebook.com" />
                  </div>
                  <span class="text-danger" id="facebook_url_error"></span>
                  @if ($errors->has('facebook_url'))
                  <span class="text-danger">{{ $errors->first('facebook_url') }}</span>
                  @endif
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="instagram_url">Instagram</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-instagram"></i></span>
                    <input type="url" class="form-control" name="instagram_url" id="instagram_url"
                      value="{{ old('instagram_url', $adminFormUser->instagram_url ?? '') }}" placeholder="https://instagram.com" />
                  </div>
                  <span class="text-danger" id="instagram_url_error"></span>
                  @if ($errors->has('instagram_url'))
                  <span class="text-danger">{{ $errors->first('instagram_url') }}</span>
                  @endif
                </div>
              </div>
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="twitter_url">Twitter</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-twitter"></i></span>
                    <input type="url" class="form-control" name="twitter_url" id="twitter_url"
                      value="{{ old('twitter_url', $adminFormUser->twitter_url ?? '') }}" placeholder="https://twitter.com" />
                  </div>
                  <span class="text-danger" id="twitter_url_error"></span>
                  @if ($errors->has('twitter_url'))
                  <span class="text-danger">{{ $errors->first('twitter_url') }}</span>
                  @endif
                </div>
              </div>
              <!-- <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="pricing_plan_id">Pricing Plan</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-list-ul"></i></span>
                    <select name="pricing_plan_id" id="pricing_plan_id" class="form-select">
                        <option value="">Select Pricing Plan</option>
                        @foreach($pricings as $pricing)
                            <option value="{{ $pricing->id }}" {{ (isset($adminFormUser) && $adminFormUser->pricing_plan_id == $pricing->id) || old('pricing_plan_id') == $pricing->id ? 'selected' : '' }}>
                                {{ $pricing->name }}
                            </option>
                        @endforeach
                    </select>
                  </div>
                  @if ($errors->has('pricing_plan_id'))
                  <span class="text-danger">{{ $errors->first('pricing_plan_id') }}</span>
                  @endif
                </div>
              </div> -->
          <div class="col-12">
            <div class="mb-3">
              <div class="d-flex pt-3 justify-content-end">
                <a href="{{route("admin-users.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
                <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user">Save</button>
              </div>
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
@section('scripts')
@include('account_settings.scripts.profile-form-validation', [
    'formSelector' => '#admin_user_frm',
    'phoneSelector' => '#mobile',
    'phoneErrorSelector' => '#mobile_error',
    'firstNameSelector' => '#first_name',
    'lastNameSelector' => '#last_name',
])
@endsection
