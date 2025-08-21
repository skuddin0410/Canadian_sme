@extends('layouts.admin')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Profile/</span>Information</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Profile Information</h5>
        </div>

          @php
            $admin_user = auth()->user()->load('photo');
            if( isset($admin_user->photo->file_path) ){
              $filepath = $admin_user->photo->file_path;
            }else{
              $filepath = "https://via.placeholder.com/150";
            }

          @endphp

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
          <form id="account_information_frm" action="{{route('change.account.information.post')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}


            <div class="row">

          <div class="text-left">
            <input type="file" id="profileImageInput" name="image" accept="image/*" class="d-none">
            <label for="profileImageInput">
              <img id="profileImagePreview" 
                   src="{{$filepath}}" 
                   class="rounded-circle border border-2" 
                   style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;">
            </label>
            
            <p class="mt-2 text-muted">Click image to upload</p>
          </div>

            <div class="col-6">  
            <div class="mb-3">
              <label class="form-label" for="name">First Name</label>
              <div class="input-group input-group-merge">
                
                <input
                  type="text"
                  class="form-control"
                  name="name"
                  id="name"
                  value="{{old('name', $admin_user->name)}}"
                  placeholder="Full Name"/>
              </div>
              @if ($errors->has('name'))
                <span class="text-danger text-left">{{ $errors->first('name') }}</span>
              @endif
            </div>
            </div>

            <div class="col-6">  
            <div class="mb-3">
              <label class="form-label" for="lastname">Last Name</label>
              <div class="input-group input-group-merge">
                
                <input
                  type="text"
                  class="form-control"
                  name="lastname"
                  id="lastname"
                  value="{{old('lastname', $admin_user->lastname)}}"
                  placeholder="Full Name"/>
              </div>
              @if ($errors->has('lastname'))
                <span class="text-danger text-left">{{ $errors->first('lastname') }}</span>
              @endif
            </div>
            </div>

             <div class="col-6"> 
              <div class="mb-3">
              <label class="form-label" for="email">Email</label>
              <div class="input-group input-group-merge">
                
                <input
                  type="text"
                  name="email"
                  id="email"
                  class="form-control"
                  value="{{old('email', $admin_user->email)}}"
                  placeholder="Email" disabled/>
              </div>
              @if ($errors->has('email'))
                <span class="text-danger text-left">{{ $errors->first('email') }}</span>
              @endif
            </div>
            </div>

            <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="contact_number">Contact Number</label>
              <div class="input-group input-group-merge">
        
                <input
                  type="text"
                  name="contact_number"
                  id="contact_number"
                  class="form-control phone-mask"
                  placeholder=""
                  value="{{old('contact_number', $admin_user->mobile)}}" />
              </div>
              <span class="text-danger text-left" id="contact_number_error"></span>
              @if ($errors->has('contact_number'))
                <span class="text-danger text-left">{{ $errors->first('contact_number') }}</span>
              @endif
            </div>
          </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="contact_number">Designation</label>
              <div class="input-group input-group-merge">
        
                <input
                  type="text"
                  name="designation"
                  id="designation"
                  class="form-control phone-mask"
                  placeholder=""
                  value="{{old('designation', $admin_user->designation)}}" />
              </div>
              <span class="text-danger text-left" id="designation_error"></span>
              @if ($errors->has('designation'))
                <span class="text-danger text-left">{{ $errors->first('designation') }}</span>
              @endif
            </div>
          </div>

            <div class="col-6">
              <div class="mb-3">
                <label class="form-label" for="contact_number">Role</label>
                <div class="input-group input-group-merge">
                  {{auth()->user()->getRoleNames()}}
                </div>
            </div>
            </div>

            <div class="col-12">
            <div class="mb-3">
              <label class="form-label" for="contact_number">Website url</label>
              <div class="input-group input-group-merge">
         
                <input
                  type="text"
                  name="website_url"
                  id="website_url"
                  class="form-control phone-mask"
                  placeholder=""
                  value="{{old('website_url', $admin_user->website_url)}}" />
              </div>
              <span class="text-danger text-left" id="website_url_error"></span>
              @if ($errors->has('website_url'))
                <span class="text-danger text-left">{{ $errors->first('website_url') }}</span>
              @endif
            </div>
          </div>


          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="contact_number">Linkedin url</label>
              <div class="input-group input-group-merge">
             
                <input
                  type="text"
                  name="linkedin_url"
                  id="linkedin_url"
                  class="form-control phone-mask"
                  placeholder=""
                  value="{{old('linkedin_url', $admin_user->linkedin_url)}}" />
              </div>
              <span class="text-danger text-left" id="linkedin_url_error"></span>
              @if ($errors->has('linkedin_url'))
                <span class="text-danger text-left">{{ $errors->first('linkedin_url') }}</span>
              @endif
            </div>
          </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="contact_number">Facebook url</label>
              <div class="input-group input-group-merge">
      
                <input
                  type="text"
                  name="facebook_url"
                  id="facebook_url"
                  class="form-control phone-mask"
                  placeholder=""
                  value="{{old('facebook_url', $admin_user->facebook_url)}}" />
              </div>
              <span class="text-danger text-left" id="facebook_url_error"></span>
              @if ($errors->has('facebook_url'))
                <span class="text-danger text-left">{{ $errors->first('facebook_url') }}</span>
              @endif
            </div>
          </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="contact_number">Instagram url</label>
              <div class="input-group input-group-merge">
        
                <input
                  type="text"
                  name="instagram_url"
                  id="instagram_url"
                  class="form-control phone-mask"
                  placeholder=""
                  value="{{old('instagram_url', $admin_user->instagram_url)}}" />
              </div>
              <span class="text-danger text-left" id="instagram_url_error"></span>
              @if ($errors->has('instagram_url'))
                <span class="text-danger text-left">{{ $errors->first('instagram_url') }}</span>
              @endif
            </div>
          </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="contact_number">Twitter url</label>
              <div class="input-group input-group-merge">
     
                <input
                  type="text"
                  name="twitter_url"
                  id="twitter_url"
                  class="form-control phone-mask"
                  placeholder=""
                  value="{{old('twitter_url', $admin_user->twitter_url)}}" />
              </div>
              <span class="text-danger text-left" id="twitter_url_error"></span>
              @if ($errors->has('twitter_url'))
                <span class="text-danger text-left">{{ $errors->first('twitter_url') }}</span>
              @endif
            </div>
          </div>



            </div>

            <input type="button" id="account_info_submit_btn" value="Save" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user"/>
            <a href="{{route('home')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Cancel</a>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
document.getElementById("profileImageInput").addEventListener("change", function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("profileImagePreview").src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection

@section('scripts')
@include('account_settings.scripts.index')
@endsection