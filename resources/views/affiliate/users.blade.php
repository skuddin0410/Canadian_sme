@extends('layouts.admin')
@section('content')

<div class="container align-items-center grey-bg">
  <section>
    <div class="row">
      <div class="col-12 mt-3 mb-1">
        <h5>Latest Earning status</h5>
      </div>
    </div>

    <div class="row">
      <div class="col-12 text-right">
        <form action="{{route('affiliate.users')}}" method="POST">
          @csrf
          <div class="row padding-none">
            <div class="col-4">
            </div>
            <div class="col-3">
             
            </div>
            <div class="col-3">
              <div class="mb-3">
                <input type="text" class="form-control" name="search" value="" id="search" placeholder="Search" required/>
              </div>
            </div>
            <div class="col-2 text-center">
              <a href="{{route('affiliate.users')}}" class="btn btn-primary filter">Cancel</a> 
              <button type="submit" class="btn btn-outline-primary btn-pill">Filter</button>
            </div>
          </div>
        </form>
      </div>
      @include('affiliate.table')
    </div>
  </section>
</div>

@endsection