@extends('layouts.admin')

@section('title')
    Admin | Coupons
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Coupons</span></h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Coupon @if(!empty($coupon)) Update @else Create @endif</h5>
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
       
          @if(!empty($coupon))
             <form  action="{{route('coupons.update',["coupon"=>$coupon->id])}}" method="POST" enctype="multipart/form-data">
          @else
             <form  action="{{route('coupons.store')}}" method="POST" enctype="multipart/form-data">
          @endif 

          
            {{ csrf_field() }}
             <div class="mb-3">
              <label class="form-label" for="title">Coupon Code<span class="text-danger">*(Space will not allowed)</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="name"
                  id="coupon_code"
                  value="{{$coupon->name ?? ''}}"
                  placeholder="Name"
                  style="text-transform: uppercase"
                  />
              </div>
              @if ($errors->has('name'))
                <span class="text-danger text-left">{{ $errors->first('name') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label" for="title">Price<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text">{{config('app.currency_sign')}}</span>
                <input
                  type="text"
                  class="form-control"
                  name="price"
                  id="price"
                  value="{{ $coupon->price ?? old('price') }}"
                  placeholder="Price"/>
              </div>
              @if ($errors->has('price'))
                <span class="text-danger text-left">{{ $errors->first('price') }}</span>
              @endif
            </div>
            
            <div class="mb-3">
              <label class="form-label" for="title">Type<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <select class="form-control" name="type">
                  <option value="fixed" {{!empty($coupon) && $coupon->type =='fixed' ? 'selected' : ''}} >Fixed</option>
                  <option value="percentage" {{!empty($coupon) && $coupon->type =='percentage' ? 'selected' : ''}} >Percentage</option>
                </select>
              </div>
              @if ($errors->has('type'))
                <span class="text-danger text-left">{{ $errors->first('type') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label" for="title">Expires at<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="date"
                  class="form-control"
                  name="expires_at"
                  id="expires_at"
                  value="{{!empty($coupon->expires_at) ? date('Y-m-d',strtotime($coupon->expires_at))  : old('expires_at') }}"
                  placeholder="Expires at"/>
              </div>
              @if ($errors->has('expires_at'))
                <span class="text-danger text-left">{{ $errors->first('expires_at') }}</span>
              @endif
            </div> 


            @if(!empty($coupon))
             @method('PUT')
            @endif
            <div class="d-flex pt-3 justify-content-end">
                <a href="{{route('coupons.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
                <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user">Save</button>
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
    @if(empty($coupon))
    $(document).ready(function(){
      $("#coupon_code").val("SME"+Math.floor(10000 + Math.random() * 90000)+Math.floor(1000 + Math.random() * 9000)); 
    }); 
    @endif        
</script>
@endsection