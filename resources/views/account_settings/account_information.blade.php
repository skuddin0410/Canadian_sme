@extends('layouts.admin')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Account/</span>Account Information</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Account Information</h5>
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
          <form id="account_information_frm" action="{{route('change.account.information.post')}}" method="POST">
            {{ csrf_field() }}
            @php
            $admin_user = auth()->user();
            @endphp
            <div class="mb-3">
              <label class="form-label" for="name">Full Name</label>
              <div class="input-group input-group-merge">
                <span id="basic-icon-default-fullname2" class="input-group-text"
                  ><i class="bx bx-user"></i
                ></span>
                <input
                  type="text"
                  class="form-control"
                  name="name"
                  id="name"
                  value="{{old('name', $admin_user->name .' '.$admin_user->lastname)}}"
                  placeholder="Full Name"/>
              </div>
              @if ($errors->has('name'))
                <span class="text-danger text-left">{{ $errors->first('name') }}</span>
              @endif
            </div>
              <div class="mb-3">
              <label class="form-label" for="email">Email</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
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
              <!-- <div class="form-text">You can use letters, numbers & periods</div> -->
            </div>
            <div class="mb-3">
              <label class="form-label" for="contact_number">Contact Number</label>
              <div class="input-group input-group-merge">
                <span id="basic-icon-default-phone2" class="input-group-text"
                  ><i class="bx bx-phone"></i
                ></span>
                <input
                  type="text"
                  name="contact_number"
                  id="contact_number"
                  class="form-control phone-mask"
                  placeholder="658 799 8941"
                  value="{{old('contact_number', $admin_user->mobile)}}" />
              </div>
              <span class="text-danger text-left" id="contact_number_error"></span>
              @if ($errors->has('contact_number'))
                <span class="text-danger text-left">{{ $errors->first('contact_number') }}</span>
              @endif
            </div>
            <input type="button" id="account_info_submit_btn" value="Save" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user"/>
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