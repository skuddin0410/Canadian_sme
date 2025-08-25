<div class="container-xxl flex-grow-1 container-p-y pt-0">  
<h4 class="py-3 mb-4"><span class="text-muted fw-light">Change Password</h4>

<form  action="{{route('change.user.password')}}" method="POST">
  @csrf
  
<div class="row">
  <div class="col-xl">
      <div class="card mb-4">
          <div class="card-body">
            <div class="row">
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label" for="title">New Password<span class="text-danger">*</span></label>
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
              <input value="{{ !empty($user) ? $user->id : ''}}" name="user_id" type="hidden" />
              <div class="col-12">
                <div class="mb-3">
                  <div class="d-flex pt-3 justify-content-end">
                    <button type="submit" class="btn btn-primary">Save</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> 
  </form>

</div>