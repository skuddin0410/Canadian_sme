@extends('layouts.admin')
@section('content')

<div class="container align-items-center grey-bg">
  <section>
    <div class="row">
      <div class="col-12 mt-3 mb-1">
        
      </div>
    </div>
    <div class="row">
      <div class="col-xl-6 col-sm-6 col-12"> 
        <div class="card text-bg-primary">
          <div class="card-content">
            <div class="card-body">
              <div class="media d-flex justify-content-center">
                <div class="align-self-center">
                  <i class="icon-pencil primary font-large-2 float-left"></i>
                </div>
                <div class="media-body text-center">
                  <h3 class="success text-white"> {{$referrals}}</h3>
                  <span>Total Earnings</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

     <div class="col-xl-6 col-sm-6 col-12">
        <div class="card text-bg-info">
          <div class="card-content">
            <div class="card-body">
              <div class="mediad-flex justify-content-center">
                <div class="media-body text-center">
                  <h3 class="success text-white">{{$userCount ?? ''}}</h3>
                  <span>Total Users</span>
                </div>
                <div class="align-self-center">
                  <i class="icon-user success font-large-2 float-right"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <div class="row">
      <div class="col-12 mt-3 mb-1">
        <h5>Latest Earning status</h5>
      </div>
    </div>
     @include('affiliate.table')

      </div>
    </div>
  </section>
</div>

@endsection
