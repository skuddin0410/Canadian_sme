@extends('layouts.admin')

@section('title')
Admin | Add Attendee
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Attendee/</span>Create</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Attendee @if(!empty($user)) Update @else Create @endif</h5>
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

          <form
            action="@if(!empty($user)) {{ route('attendee-users.update',['user'=>$user->id]) }} @else {{ route('attendee-users.store') }} @endif "
            method="POST" enctype="multipart/form-data">
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
                      value="{{$user->lastname ?? old('last_name')}}" placeholder="User last name" />
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
                      value="{{ $user->email ?? old('email') }}" placeholder="User email" />
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
                      value="{{ $user->designation ?? old('designation') }}" placeholder="Enter designation" />
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
                      value="{{ old('tags', isset($user) ? $user->tags : '') }}" data-role="tagsinput"
                      placeholder="Add tags (comma separated)" />
                  </div>
                  @if ($errors->has('tags'))
                  <span class="text-danger text-left">{{ $errors->first('tags') }}</span>
                  @endif
                </div>
              </div>
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="website_url">Website URL</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-link"></i></span>
                    <input type="url" class="form-control" name="website_url" id="website_url"
                      value="{{ old('website_url') }}" placeholder="https://example.com" />
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
                      value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/username" />
                  </div>
                  @if ($errors->has('linkedin_url'))
                  <span class="text-danger">{{ $errors->first('linkedin_url') }}</span>
                  @endif
                </div>
              </div>

              <input type="hidden" name="user_type" value="Attendee"/>

            </div>

            <div class="col-12">
              <div class="mb-3">
                <div class="d-flex pt-3 justify-content-end">
                  <a href="{{route('attendee-users.index')}}"
                    class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
                  <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user"><i
                      class="bx bx-save"></i>Save</button>
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
    console.log(Text + " " + Last);
    if (Last != undefined && Text != undefined) {
      Text = Text + " " + Last;
      Text = slugify(Text);
      $("#slug-target").val(Text);
    }
  });
  $("#slug-source").keyup(function() {
    var Text = $('#slug-source').val();
    var Last = $('#last-name-target').val();
    console.log(Text + " " + Last);
    if (Last != undefined && Text != undefined) {
      Text = Text + " " + Last;
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