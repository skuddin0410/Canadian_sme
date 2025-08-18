@extends('layouts.admin')

@section('title')
    Admin | Speaker Add
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Speaker/</span>Create</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Speaker @if(!empty($user)) Update @else Create @endif</h5>
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
           
            <form  action="@if(!empty($user)) {{ route('speaker.create',['user'=>$user->id]) }} @else {{ route('speaker.store') }} @endif " method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
             @if(!empty($user))
             @method('PUT')
            @endif
            <div class="row">
            <div class="col-6">
             <div class="mb-3">
              <label class="form-label" for="title">First Name<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="first_name"
                  id="slug-source"
                  value="{{$user->name ?? old('first_name')}}"
                  placeholder="User first name"/>
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
                    <input
                      type="text"
                      class="form-control"
                      name="last_name"
                      id="last-name-target"
                      value="{{$user->lastname ?? old('last_name')}}"
                      placeholder="User last name"
                      />
                  </div>
                  @if ($errors->has('last_name'))
                    <span class="text-danger text-left">{{ $errors->first('last_name') }}</span>
                  @endif
            </div>
           </div>
          <div class="col-6">
             <div class="mb-3">
              <label class="form-label" for="title">User Name<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="username"
                  id="slug-target"
                  value="{{$user->username ?? old('username')}}"
                  placeholder="User name"/>
              </div>
              @if ($errors->has('username'))
                <span class="text-danger text-left">{{ $errors->first('username') }}</span>
              @endif
            </div>
          </div>
            <div class="col-6">
             <div class="mb-3">
              <label class="form-label" for="title">Email<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="email"
                  id="email"
                  value="{{ $user->email ?? old('email') }}"
                  placeholder="User email"/>
              </div>
              @if ($errors->has('email'))
                <span class="text-danger text-left">{{ $errors->first('email') }}</span>
              @endif
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
                    value="{{ $user->designation ?? old('designation') }}"
                    placeholder="Enter designation"/>
            </div>
            @if ($errors->has('designation'))
                <span class="text-danger text-left">{{ $errors->first('designation') }}</span>
            @endif
        </div>
    </div>

    <!-- Tags -->
    {{-- <div class="col-6">
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
                    placeholder="Add tags (comma separated)"/>
            </div>
            @if ($errors->has('tags'))
                <span class="text-danger text-left">{{ $errors->first('tags') }}</span>
            @endif
        </div>
    </div> --}}
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
                value="{{ old('tags', isset($user) ? $user->tags : '') }}"
                data-role="tagsinput"
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
                        <textarea name="bio" id="bio" class="form-control" placeholder="Speaker Bio" rows="8">{{ old('bio') }}</textarea>
                        @if ($errors->has('bio'))
                            <span class="text-danger">{{ $errors->first('bio') }}</span>
                        @endif

    </div>
  </div>

          <div class="col-6">
    <div class="mb-3">
        <label class="form-label" for="website_url">Website URL</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="bx bx-link"></i></span>
            <input type="url" class="form-control" name="website_url" id="website_url"
                   value="{{ old('website_url') }}" placeholder="https://example.com"/>
        </div>
        @if ($errors->has('website_url'))
            <span class="text-danger">{{ $errors->first('website_url') }}</span>
        @endif
    </div>
</div>

<div class="col-6">
    <div class="mb-3">
        <label class="form-label" for="linkedin_url">LinkedIn URL</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="bx bxl-linkedin"></i></span>
            <input type="url" class="form-control" name="linkedin_url" id="linkedin_url"
                   value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/username"/>
        </div>
        @if ($errors->has('linkedin_url'))
            <span class="text-danger">{{ $errors->first('linkedin_url') }}</span>
        @endif
    </div>
</div>

          <div class="col-6">
            <div class="mb-3">
                  <label class="form-label" for="title">Mobile<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      name="mobile"
                      id="mobile"
                      value="{{ $user->mobile ?? old('mobile') }}"
                      placeholder="User mobile"
                      />
                  </div>
                  @if ($errors->has('mobile'))
                    <span class="text-danger text-left">{{ $errors->first('mobile') }}</span>
                  @endif
            </div>
           </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="title">DOB<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="date"
                  class="form-control"
                  name="dob"
                  id="dob"
                  value="{{ !empty($user) && $user->dob ? userDateFormat($user->dob) : old('dob') }}"
                  placeholder="Event at"/>
              </div>
              @if ($errors->has('dob'))
                <span class="text-danger text-left">{{ $errors->first('dob') }}</span>
              @endif
            </div> 
          </div>
          <div class="col-6">
          <div class="mb-3">
              <label class="form-label" for="title">Gender</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-chevron-down"></i></span>
                <select class="form-control" name="gender">
                 <option value="male" {{ !empty($user) && $user->gender=='male' ? 'selected' : '' }}  {{ old('dob') =='male' ? 'selected' : '' }}>Male</option>
                 <option value="female" {{ old('dob') =='female' ? 'selected' : '' }} {{ !empty($user) && $user->gender=='female' ? 'selected' : '' }}>Female</option>
                </select>
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="title">Place</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="place"
                  id="place"
                  value="{{ $user->place ?? old('place') }}"
                  placeholder="Place"/>
              </div>
              @if ($errors->has('place'))
                <span class="text-danger text-left">{{ $errors->first('place') }}</span>
              @endif
            </div> 
          </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="title">Street</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="street"
                  id="street"
                  value="{{ $user->street ?? old('street') }}"
                  placeholder="Street"/>
              </div>
              @if ($errors->has('street'))
                <span class="text-danger text-left">{{ $errors->first('street') }}</span>
              @endif
            </div> 
          </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="title">Zipcode</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="zipcode"
                  id="zipcode"
                  value="{{ $user->zipcode ?? old('zipcode') }}"
                  placeholder="Zipcode"/>
              </div>
              @if ($errors->has('zipcode'))
                <span class="text-danger text-left">{{ $errors->first('zipcode') }}</span>
              @endif
            </div> 
          </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="title">City</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="city"
                  id="city"
                  value="{{ $user->city ?? old('city') }}"
                  placeholder="City"/>
              </div>
              @if ($errors->has('city'))
                <span class="text-danger text-left">{{ $errors->first('city') }}</span>
              @endif
            </div> 
          </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="title">State</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="state"
                  id="state"
                  value="{{ $user->state ?? old('state') }}"
                  placeholder="State"/>
              </div>
              @if ($errors->has('state'))
                <span class="text-danger text-left">{{ $errors->first('state') }}</span>
              @endif
            </div> 
          </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="title">Country</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="country"
                  id="country"
                  value="{{ $user->country ?? old('country') }}"
                  placeholder="Country"/>
              </div>
              @if ($errors->has('country'))
                <span class="text-danger text-left">{{ $errors->first('country') }}</span>
              @endif
            </div> 
          </div>
          
          <div class="col-6">
          <div class="mb-3">
              <label class="form-label" for="title">Role</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-chevron-down"></i></span>
                <select class="form-control" name="user_type">
                 <option value="">Please Select Role</option> 

                  <option value="Speaker" {{ old('user_type') =='Speaker' ? 'selected' : '' }} {{ !empty($user) && !empty($user->roles) && $user->roles[0]->name=='Speaker' ? 'selected' : '' }}>Speaker</option> 
                 
                </select>

              </div>
            </div>
           
</div>

          </div>
         
          <div class="col-12">
            <div class="mb-3">
              <div class="d-flex pt-3 justify-content-end">
                <a href="{{route('speaker.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
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
<script>
$("#last-name-target").keyup(function() {
      var Text = $('#slug-source').val();
      var Last = $('#last-name-target').val();
      console.log(Text+" "+Last);
      if(Last != undefined && Text != undefined){
        Text = Text+" "+Last;
        Text = slugify(Text);
        $("#slug-target").val(Text); 
      }       
  });

$("#slug-source").keyup(function() {
      var Text = $('#slug-source').val();
      var Last = $('#last-name-target').val();
      console.log(Text+" "+Last);
      if(Last != undefined && Text != undefined){
        Text = Text+" "+Last;
        Text = slugify(Text);
        $("#slug-target").val(Text); 
      }       
  });
// $("#slug-target").keyup(function() {
//       var Text = $('#slug-source').val();
//       var Last = $('#last-name-target').val();
//       console.log(Text+" "+Last);
//       if(Last != undefined && Text != undefined){
//         Text = Text+" "+Last;
//         Text = slugify(Text);
//         $("#slug-target").val(Text); 
//       }       
//   });


function slugify(str) {
  str = str.replace(/^\s+|\s+$/g, ''); // trim leading/trailing white space
  str = str.toLowerCase(); // convert string to lowercase
  str = str.replace(/[^a-z0-9 -]/g, '') // remove any non-alphanumeric characters
           .replace(/\s+/g, '-') // replace spaces with hyphens
           .replace(/-+/g, '-'); // remove consecutive hyphens
  return str.replace(/^-+|-+$/g, '');
}

</script>
@endsection