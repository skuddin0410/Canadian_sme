@extends('layouts.frontendapp')

@section('title', config('app.name'))
@section('content')
    <section class="schedule">
        <div class="container">
                <section>
                  <div class="container">
                    <div class="row justify-content-center text-center">
                      <div class="col-lg-10 col-xl-8">
                        <h2 class="mb-2">{{$page->name ?? ''}}</h2>
                      </div>
                    </div>
                  </div>
                </section>

            <div class="schedule-box mt-4 mt-lg-5 d-flex flex-column">
               {{$page->description ?? ''}}
            </div>
          
        </div>
    </section>
@endsection