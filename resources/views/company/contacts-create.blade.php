@extends('layouts.admin')

@section('title')
    Admin | Contact
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Add/</span>Contact</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Contact Create</h5>
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
          <form  action="{{ route('company.contacts.store') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }} 
          
            <div class="row">
            <div class="col-12">
             <div class="mb-3">
              <label class="form-label" for="name">Name<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="name-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                
              </div>
              @if ($errors->has('name'))
                <span class="text-danger text-left">{{ $errors->first('name') }}</span>
              @endif
            </div>
          </div>
          <div class="col-6">
            <div class="mb-3">
                  <label class="form-label" for="Email">Email<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="Email-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      name="email"
                      value="{{ old('email') }}"
                      placeholder="Contact email"
                      />
                  </div>
                  @if ($errors->has('email'))
                    <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                  @endif
            </div>
           </div>

           <div class="col-6">
            <div class="mb-3">
                  <label class="form-label" for="Phone">Phone<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="Phone-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      name="phone"
                      id="phone-target"
                      value="{{ old('phone') }}"
                      placeholder="phone"
                      />
                  </div>
                  @if ($errors->has('phone'))
                    <span class="text-danger text-left">{{ $errors->first('phone') }}</span>
                  @endif
            </div>
           </div>
           </div>


          <div class="d-flex pt-3 justify-content-end">
             <a href="{{route('company.contacts.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user">Save</button>
          </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
