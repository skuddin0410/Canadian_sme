@extends('layouts.admin')

@section('title')
    Admin |  Admin user
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light"> Admin user/</span>@if(!empty($user)) Update @else Create @endif</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"> Admin user @if(!empty($user)) Update @else Create @endif</h5>
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
           
            <form  action="@if(!empty($user)) {{ route('admin-users.update',['admin_user'=>$user->id]) }} @else {{ route('admin-users.store') }} @endif " method="POST" autocomplete="off" enctype="multipart/form-data" >
            {{ csrf_field() }}
             @if(!empty($user))
             @method('PUT')
            @endif
            <div class="row"> 

            <div class="text-left">
              <input type="file" id="profileImageInput" name="image" accept="image/*" class="d-none">
              <label for="profileImageInput">
                <img id="profileImagePreview" 
                     src="{{!empty($user->photo) ? $user->photo->file_path : ''}}" 
                     class="rounded-circle border border-2" 
                     style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;">
              </label>
              <p class="mt-2 text-muted">Click image to upload</p>
            </div>
             
            <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="title">First Name<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input type="text" class="form-control" name="first_name" id="slug-source"
                      value="{{$user->name ?? old('first_name')}}" placeholder="User first name" />
                  </div>
                  @if ($errors->has('first_name'))
                  <span class="text-danger text-left">{{ $errors->first('first_name') }}</span>
                  @endif
                </div>
              </div>
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="title">last name<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input type="text" class="form-control" name="last_name" id="last-name-target"
                      value="{{$user->lastname ?? old('last_name') }}" placeholder="User last name" />
                  </div>
                  @if ($errors->has('last_name'))
                  <span class="text-danger text-left">{{ $errors->first('last_name') }}</span>
                  @endif
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="title">Email<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input type="text" class="form-control" name="email" id="email"
                      value="{{$user->email ?? old('email') }}" placeholder="User email" />
                  </div>
                  @if ($errors->has('email'))
                  <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                  @endif
                </div>
              </div>

               <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="title">Mobile<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input type="text" class="form-control" name="mobile" id="mobile"
                      value="{{ $user->mobile ?? old('mobile') }}" placeholder="User mobile" />
                  </div>
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
                      value="{{$user->designation ?? old('designation') }}" placeholder="Enter designation" />
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
                      value="{{$user->tags ?? old('tags') }}" data-role="tagsinput"
                      placeholder="Add tags (comma separated)" />
                  </div>
                  @if ($errors->has('tags'))
                  <span class="text-danger text-left">{{ $errors->first('tags') }}</span>
                  @endif
                </div>
              </div>

            <div class="col-12">
              <div class="mb-3">
                 <label class="form-label">Bio <span class="text-danger">*</span></label>
                  <textarea name="bio" id="bio" class="form-control" placeholder="Speaker Bio" rows="8">{{$user->bio ?? old('bio') }}</textarea>
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
                    <input type="text" class="form-control" name="website_url" id="website_url"
                      value="{{$user->website_url ?? old('website_url') }}" placeholder="https://example.com" />
                  </div>
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
                    <input type="text" class="form-control" name="linkedin_url" id="linkedin_url"
                      value="{{$user->linkedin_url ?? old('linkedin_url') }}" placeholder="https://linkedin.com/in/username" />
                  </div>
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
                    <input type="text" class="form-control" name="facebook_url" id="facebook_url"
                      value="{{ $user->facebook_url ?? old('facebook_url') }}" placeholder="https://facebook.com" />
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
                    <input type="text" class="form-control" name="instagram_url" id="instagram_url"
                      value="{{$user->instagram_url ?? old('instagram_url') }}" placeholder="https://instagram.com" />
                  </div>
                  @if ($errors->has('instagram_url'))
                  <span class="text-danger">{{ $errors->first('instagram_url') }}</span>
                  @endif
                </div>
              </div>
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="twitter">Twitter</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-twitter"></i></span>
                    <input type="text" class="form-control" name="twitter_url" id="twitter_url"
                      value="{{ $user->twitter_url ?? old('twitter_url') }}" placeholder="https://twitter.com" />
                  </div>
                  @if ($errors->has('twitter_url'))
                  <span class="text-danger">{{ $errors->first('twitter_url') }}</span>
                  @endif
                </div>
              </div>
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
