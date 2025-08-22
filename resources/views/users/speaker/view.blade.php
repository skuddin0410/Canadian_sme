@extends('layouts.admin')

@section('title')
Admin | Speaker Details
@endsection
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>Speaker</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div class="dt-action-buttons text-end pt-3 pt-md-0">
                  <div class="dt-buttons"> 
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex pt-3 justify-content-end">
                     
                      <a href="{{route("speaker.index")}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                </div>
                <div class="d-flex pt-3 justify-content-end">
                     {{-- Block Button for Admin / Admin --}}
                   @if(Auth::user()->hasAnyRole(['Admin','Admin']) 
                     && !$user->is_block 
                        && $user->hasAnyRole(['Admin','Representative','Attendee','Speaker']))
                   <form action="{{ route('users.toggleBlock', $user->id) }}" method="POST">
                      @csrf
                    @method('PATCH')
                  <button type="submit" class="btn btn-danger btn-pill btn-streach font-book fs-14">
                    Block User
                  </button>
                  </form>
                  @endif


    @if(Auth::user()->hasRole('Support Staff Or Helpdesk') && $user->is_block)
        <form action="{{ route('helpdesk.users.unblock', $user->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success btn-pill btn-streach font-book fs-14">
                Unblock User
            </button>
        </form>
    @endif
    </div>
                <h5 class="pb-2 border-bottom mb-4">Speaker Details</h5>

                <div class="info-container">
                       <div class="row">   

                        <div class="text-left mb-2">
                            <label for="profileImageInput">
                              <img id="profileImagePreview" 
                                   src="{{!empty($user->photo) ? $user->photo->file_path : ''}}" 
                                   class="rounded-circle border border-2" 
                                   style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;">
                            </label>
                        </div>    
                        <div class="col-12 mt-2">
                            <ul class="list-unstyled justify-content-between">
                            <li class="mb-3"><span class="fw-medium me-2">Bio:</span><span>{{$user->bio ?? ''}}</span></li>
                             </ul>
                        </div>  
                        <div class="col-6">
                                <ul class="list-unstyled justify-content-between">
                                    <li class="mb-3"><span class="fw-medium me-2">Name:</span> <span>{{ $user->name }}
                                            {{ $user->lastname }}</span></li>
                                   
                                    <li class="mb-3"><span class="fw-medium me-2">Email:</span>
                                        <span>{{ $user->email }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">Designation:</span>
                                        <span>{{ $user->designation ?? '-' }}</span></li>
                                    <li class="mb-3"><span class="fw-medium me-2">Mobile:</span>
                                        <span>{{ $user->mobile ?? '' }}</span></li>

                                    <li class="mb-3"><span class="fw-medium me-2">Tags:</span>
                                        @if(!empty($user->tags))
                                        @foreach(explode(',', $user->tags) as $tag)
                                        <span class="badge bg-primary me-1">{{ trim($tag) }}</span>
                                        @endforeach
                                        @else
                                        <span>-</span>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                            <div class="col-6">
                                <ul class="list-unstyled justify-content-between">
                                    <li class="mb-3"><span class="fw-medium me-2">Website:</span> <span>{{ $user->website_url }}</span></li>
                                   
                                   <li class="mb-3"><span class="fw-medium me-2">Linkedin:</span> <span>{{ $user->linkedin_url }}</span></li>

                                   <li class="mb-3"><span class="fw-medium me-2">Facebook:</span> <span>{{ $user->facebook_url }}</span></li>
                                   <li class="mb-3"><span class="fw-medium me-2">Instagram:</span> <span>{{ $user->instagram_url }}</span></li>
                                   <li class="mb-3"><span class="fw-medium me-2">Twitter:</span> <span>{{ $user->twitter_url }}</span></li> 
                                </ul>
                            </div>

                        </div>
                    </div>

                   
                
                
                </div>
              </div>
          </div>
		</div>
    </div>
</div>

@include('users.ChangeUserPassword')
@endsection
