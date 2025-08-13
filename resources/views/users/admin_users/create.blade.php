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
              <label class="form-label" for="title">User Name<span class="text-danger">*(Space will not allowed)</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="username"
                  id="slug-target"
                  value="{{$user->username ?? old('username')}}"
                  placeholder="User name"
                  {{!empty($user) && $user->username ? 'readonly' : '' }}
                  
                  />
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
                  placeholder="User email"
                  {{!empty($user) && $user->email ? ' readonly':'' }}
                  />
              </div>
              @if ($errors->has('email'))
                <span class="text-danger text-left">{{ $errors->first('email') }}</span>
              @endif
            </div>
          </div>
          <div class="col-12">
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
                      {{!empty($user) && $user->mobile ? 'readonly':'' }}
                      />
                  </div>
                  @if ($errors->has('mobile'))
                    <span class="text-danger text-left">{{ $errors->first('mobile') }}</span>
                  @endif
            </div>
           </div>
           {{-- <div class="col-6">
            <div class="mb-3">
                  <label class="form-label" for="title">Referral coupon<span class="text-danger">* (Space will not allowed)</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      name="referral_coupon"
                      id="referral_coupon"
                      value="{{ $user->referral_coupon ?? old('referral_coupon') }}"
                      placeholder="User referral coupon"
                      {{!empty($user) && $user->referral_coupon ? ' readonly':'' }}
                      />
                  </div>
                  @if ($errors->has('referral_coupon'))
                    <span class="text-danger text-left">{{ $errors->first('referral_coupon') }}</span>
                  @endif
            </div>
           </div> --}}
           <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="title">Password<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="password"
                  class="form-control"
                  name="password"
                  id="password"
                  autocomplete="off"
                  placeholder="Password"/>
              </div>
              @if ($errors->has('password'))
                <span class="text-danger text-left">{{ $errors->first('password') }}</span>
              @endif
            </div> 
          </div>

          <div class="col-6">
            <div class="mb-3">
              <label class="form-label" for="title">Confirm password<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="confirm_password"
                  class="form-control"
                  name="confirm_password"
                  id="confirm_password"
                  value=""
                  autocomplete="off"
                  placeholder="Confirm password"/>
              </div>
              @if ($errors->has('confirm_password'))
                <span class="text-danger text-left">{{ $errors->first('confirm_password') }}</span>
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
@section('scripts')
<script>
// $("#last-name-target").keyup(function() {
//       var Text = $('#slug-source').val();
//       var Last = $('#last-name-target').val();
//       console.log(Text+" "+Last);
//       if(Last != undefined && Text != undefined){
//         Text = Text+" "+Last;
//         Text = slugify(Text);
//         $("#slug-target").val(Text); 
//       }       
//   });

// $("#slug-source").keyup(function() {
//       var Text = $('#slug-source').val();
//       var Last = $('#last-name-target').val();
//       console.log(Text+" "+Last);
//       if(Last != undefined && Text != undefined){
//         Text = Text+" "+Last;
//         Text = slugify(Text);
//         $("#slug-target").val(Text); 
//       }       
//   });

// function slugify(str) {
//   str = str.replace(/^\s+|\s+$/g, ''); // trim leading/trailing white space
//   str = str.toLowerCase(); // convert string to lowercase
//   str = str.replace(/[^a-z0-9 -]/g, '') // remove any non-alphanumeric characters
//            .replace(/\s+/g, '-') // replace spaces with hyphens
//            .replace(/-+/g, '-'); // remove consecutive hyphens
//   return str.replace(/^-+|-+$/g, '');
// }

</script>
@endsection