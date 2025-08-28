@extends('layouts.admin')

@section('title', 'Admin | Exhibitor User Details')

@section('content')
 <style>
    .tab-content .tab-pane {
      padding: 20px; /* padding for each tab content */
    }
    .nav-tabs .nav-link {
      padding: 10px 20px; /* padding for tab buttons */

    }
  </style>
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h3 class="py-3 mb-4">Exhibitor User</h3>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body">

                   <div class="d-flex justify-content-end gap-2 pt-3">

                    <form action="{{ route('users.toggleBlock', $user->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-pill btn-streach font-book fs-14">
                            Block User
                        </button>
                    </form>

                    <form action="{{ route('helpdesk.users.unblock', $user->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-warning btn-pill btn-streach font-book fs-14">
                            Unblock User
                        </button>
                    </form>

               
                    @if(!$user->is_approve)
                        <form action="{{ route('exhibitor-users.approve', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                Approve Exhibitor
                            </button>
                        </form>
                    @else
                        <span class="badge bg-success">Approved</span>
                    @endif

                    <form action="{{ route('exhibitor-users.index') }}" method="POST">
                        <button type="submit" class="btn btn-outline-primary btn-pill btn-streach font-book fs-14"> <i class="fa fa-angle-left me-1"></i>
                            Back
                        </button>
                    </form>
                
                    </div>


       
                    <h5 class="pb-2 border-bottom mt-4 mb-4">Exhibitor User Details</h5>
                    <div class="row">
                        <div class="col-12">
                            <ul class="list-unstyled">
                                <li class="mb-3"><strong>Name:</strong> {{ $user->name }} {{ $user->lastname }}</li>
                                <li class="mb-3"><strong>Email:</strong> {{ $user->email }}</li>
                                {{-- <li class="mb-3"><strong>Mobile:</strong> {{ $user->mobile ?? '' }}</li> --}}
                            </ul>
                        </div>
                            
                        
                    </div>
                    </div> 
                    <div class="p-4">

                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                              <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="company-tab" data-bs-toggle="tab" 
                                        data-bs-target="#company" type="button" role="tab">Company</button>
                              </li>
                              {{-- <li class="nav-item" role="presentation">
                                <button class="nav-link" id="booth-tab" data-bs-toggle="tab" 
                                        data-bs-target="#booth" type="button" role="tab">Booth</button>
                              </li> --}}
                              <li class="nav-item" role="presentation">
                                <button class="nav-link" id="images-tab" data-bs-toggle="tab" 
                                        data-bs-target="#images" type="button" role="tab">Images</button>
                              </li>

                              <li class="nav-item" role="presentation">
                                <button class="nav-link" id="video-tab" data-bs-toggle="tab" 
                                        data-bs-target="#video" type="button" role="tab">Videos</button>
                              </li>

                              <li class="nav-item" role="presentation">
                                <button class="nav-link" id="market-tab" data-bs-toggle="tab" 
                                        data-bs-target="#market" type="button" role="tab">Marketing</button>
                              </li>

                              <li class="nav-item" role="presentation">
                                <button class="nav-link" id="product-tab" data-bs-toggle="tab" 
                                        data-bs-target="#product" type="button" role="tab">Products</button>
                              </li>

                            </ul>

                      
                            <div class="tab-content" id="myTabContent">

                              <div class="tab-pane fade show active" id="company" role="tabpanel">
                                @if($company)
                                <h3>{{ $company->name }}</h3>
                                
                                @if($company->logoFile)
                                    <img src="{{ Storage::url($company->logoFile->file_name) }}" 
                                         class="card-img-top mb-2" style="height: 100px; object-fit: cover;">
                                @endif

                                <p><strong>Name:</strong>{{ $company->name }} </p>
                                <p><strong>Industry:</strong> {{ $company->industry }}</p>
                                <p><strong>Size:</strong> {{ $company->size }}</p>
                                <p><strong>Location:</strong> {{ $company->location }}</p>
                                <p><strong>Email:</strong> {{ $company->email }}</p>
                                <p><strong>Phone:</strong> {{ $company->phone }}</p>
                              
                                
                            @else
                                <p class="text-muted">No companies registered by this exhibitor.</p>
                            @endif
                              </div>

                              {{-- <div class="tab-pane fade" id="booth" role="tabpanel">
                                <h3>Booth Details</h3>
                                <p>This is the Profile tab content with padding.</p>
                              </div> --}}

                              <div class="tab-pane fade" id="images" role="tabpanel">
                                <h3>Media Gallery</h3>

                                @if($company->mediaGallery->count())
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @foreach($company->mediaGallery as $media)
                                        <img src="{{ Storage::url($media->file_name) }}" 
                                             style="width: 70px; height: 70px; object-fit: cover;" class="rounded">
                                    @endforeach
                                </div>
                               

                                @else
                                <p class="text-muted">No image found.</p>
                                @endif
                                
                              </div>

                              <div class="tab-pane fade" id="video" role="tabpanel">
                                <h3>Videos</h3>
                                @if($company->videos->count())
                                  
                                    @foreach($company->videos as $video)
                                        <video controls style="width: 100%; max-height: 200px;" class="mb-2">
                                            <source src="{{ Storage::url($video->file_name) }}" type="video/mp4">
                                        </video>
                                    @endforeach
                                

                                @else
                                <p class="text-muted">No video found.</p>
                                @endif
                              </div>

                              <div class="tab-pane fade" id="market" role="tabpanel">
                                <h3>Marketing</h3>
                                <p>This is the Contact tab content with padding.</p>
                              </div>

                              <div class="tab-pane fade" id="product" role="tabpanel">
                                <h3>Products</h3>
                                <p>This is the Contact tab content with padding.</p>
                              </div>

                            </div>
 
                    </div>

                    @if(!empty($user) && !empty($user->booths)) 
                    <div class="mt-5">
                        <h5 class="pb-2 border-bottom mb-3">Booth Details</h5>
                        @if ($user->booths->count())
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Booth Number</th>
                                        <th>Size</th>
                                        <th>Location Preferences</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user->booths as $booth)
                                        <tr>
                                            <td>{{ $booth->title ?? '-' }}</td>
                                            <td>{{ $booth->booth_number ?? '-' }}</td>
                                            <td>{{ $booth->size ?? '-' }}</td>
                                            <td>{{ $booth->location_preferences ?? '-' }}</td>
                                            <td>{{ $booth->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">No booths assigned to this user.</p>
                        @endif
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
