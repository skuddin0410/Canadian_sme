@extends('layouts.admin')

@section('title')
Admin | Event Details
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>Event</h4>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="dt-action-buttons text-end pt-3 pt-md-0">
                        <div class="dt-buttons"></div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="d-flex pt-3 justify-content-end">
                        <a href="{{ route("events.edit",["event"=> $event->id ]) }}"
                            class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
                        <a href="{{ route('events.index') }}"
                            class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
                    </div>

                    <h5 class="pb-2 border-bottom mb-4"><span>{{ $event->title }}</span></h5>

                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <span class="fw-medium me-2">Image:</span>
                                @if(!empty($event->photo) && $event->photo->file_path)
                                <span class="fw-medium me-2"><img src="{{asset($event->photo->file_path)  ?? ''}}"
                                        alt="banner Image" height="100px;"></span>
                                @endif
                            </li>
                            <li class="mb-3"><span class="fw-medium me-2">Location:</span>
                                <span>{{ $event->location ?? '' }}</span></li>
                            <li class="mb-3"><span class="fw-medium me-2">Status:</span>
                                <span>{{ ucfirst($event->status) }}</span></li>
                            <li class="mb-3"><span class="fw-medium me-2">Start Date:</span>
                                <span>{{ dateFormat($event->start_date) }}</span></li>
                            <li class="mb-3"><span class="fw-medium me-2">End Date:</span>
                                <span>{{dateFormat($event->end_date) }}</span></li>
                            <li class="mb-3"><span class="fw-medium me-2">Description:</span> <span>{!!
                                    $event->description ?? '' !!}</span></li>
                            <li class="mb-3"><span class="fw-medium me-2">Visibility:</span> <span>{!!
                                    $event->visibility ?? '' !!}</span></li>
                                    
                            <li class="mb-3"><span class="fw-medium me-2">Meta title:</span> <span>{!!
                                    $event->meta_title ?? '' !!}</span></li>   
                                    
                            <li class="mb-3"><span class="fw-medium me-2">Meta description:</span> <span>{!!
                                    $event->meta_description ?? '' !!}</span></li> 
                            <li class="mb-3"><span class="fw-medium me-2">Meta keywords:</span> <span>{!!
                                    $event->meta_keywords ?? '' !!}</span></li>                 
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection