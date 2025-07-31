@extends('layouts.admin')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Account/</span>Change Password</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Change Password</h5>
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
          <form id="change_password_frm" action="{{route('admin.change.password.post')}}" method="POST">
            {{ csrf_field() }}
            @php
            $admin_user = auth()->user();
            @endphp
            <div class="mb-3">
              <label class="form-label" for="old_password">Old Password</label>
              <div class="input-group input-group-merge">
                <span id="basic-icon-default-fullname2" class="input-group-text"
                  ><i class="bx bx-user"></i
                ></span>
                <input
                  type="text"
                  class="form-control"
                  name="old_password"
                  id="old_password"
                  value="{{ old('old_password') }}"
                  placeholder="Old Password"/>
              </div>
              <span class="text-danger text-left" id="old_password_error"></span>
              @if ($errors->has('old_password'))
                <span class="text-danger text-left">{{ $errors->first('old_password') }}</span>
              @endif
            </div>
              <div class="mb-3">
              <label class="form-label" for="new_password">New Password</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                <input
                  type="text"
                  name="new_password"
                  id="new_password"
                  class="form-control"
                  value="{{ old('new_password') }}"
                  placeholder="New Password"/>
              </div>
              <span class="text-danger text-left" id="new_password_error"></span>
              @if ($errors->has('new_password'))
                <span class="text-danger text-left">{{ $errors->first('new_password') }}</span>
              @endif
              <!-- <div class="form-text">You can use letters, numbers & periods</div> -->
            </div>
            <div class="mb-3">
              <label class="form-label" for="confirm_password">Confirm Password</label>
              <div class="input-group input-group-merge">
                <span id="basic-icon-default-phone2" class="input-group-text"
                  ><i class="bx bx-phone"></i
                ></span>
                <input
                  type="text"
                  name="confirm_password"
                  id="confirm_password"
                  class="form-control phone-mask"
                  placeholder="Confirm Password"
                  value="{{old('confirm_password')}}" />
              </div>
              <span class="text-danger text-left" id="confirm_password_error"></span>
              @if ($errors->has('confirm_password'))
                <span class="text-danger text-left">{{ $errors->first('confirm_password') }}</span>
              @endif
            </div>
            <input type="button" id="change_password_submit_btn" value="Save" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user"/>
            <a href="{{route('home')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Cancel</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@section('scripts')
  @include('account_settings.scripts.index')
@endsection