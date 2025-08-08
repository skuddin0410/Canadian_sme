@extends('layouts.admin')

@section('title')
Admin | Service Details
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Service/</span>Details</h4>
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

            <a href="{{route("services.edit",["service"=> $service->id ])}}"
              class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>

            <a href="{{route("services.index")}}"
              class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
          </div>
          <h5 class="pb-2 border-bottom mb-4">Service Details</h5>
          <div class="info-container">
            <ul class="list-unstyled">
              <li class="mb-3">
                <span class="fw-medium me-2">Title:</span>
                <span>{{ $service->name }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Price:</span>
                <span>{{config('app.currency_sign')}} {{ $service->price }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Description:</span>
                <span>{!! $service->description !!}</span>
              </li>
              <li class="mb-3">
                <span class="fw-medium me-2">Image:</span>
                @if(!empty($service->image_url) )
                <div class="d-flex flex-wrap gap-2">
                  <span class="fw-medium me-2"><img src="{{ asset('storage/' . $service->image_url) }}"
                      alt="{{ $service->name }}" style="width: 80px; height: 80px; object-fit: cover;"
                      class="rounded border"></span>
                </div>
                @endif
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Gallery images:</span>
                @if($service->gallery_images)
                <div class="d-flex flex-wrap gap-2">
                  @foreach($service->gallery_images as $img)
                  <span class="fw-medium me-2">
                    <img src="{{ asset('storage/' . $img) }}" style="width: 80px; height: 80px; object-fit: cover;"
                      class="rounded border">
                    @endforeach
                </div>
                @endif
              <li>

              <li class="mb-3">
                <span class="fw-medium me-2">Deliverables:</span>
                <span>{!! $service->deliverables ?? '' !!}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Capabilities:</span>
                <span>{!! $service->capabilities ?? '' !!}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Category:</span>
                <span>{{ $service->category->name ?? 'Uncategorized' }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Status:</span>
                <span class="badge bg-{{ $service->is_active ? 'success' : 'secondary' }}">
                  {{ $service->is_active ? 'Active' : 'Inactive' }}
                </span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Sort Order:</span>
                <span>
                  {{ $service->sort_order }}
                </span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Meta Title:</span>
                <span>{{ $service->meta_title }}</span>
              </li>
              <li class="mb-3">
                <span class="fw-medium me-2">Meta Description:</span>
                <span>{!! $service->meta_description !!}</span>
              </li>
              <li class="mb-3">
                <span class="fw-medium me-2">Meta Keywords:</span>
                <span>{{ $service->meta_keywords }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Created by:</span>
                <span>{{ $service->creator->full_name ?? 'System' }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Updated by:</span>
                <span>{{ $service->updater->full_name ?? '—' }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Created at:</span>
                <span>{{ $service->created_at->format('M d, Y H:i') }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Last updated:</span>
                <span>{{ $service->updated_at->format('M d, Y H:i') }}</span>
              </li>
            </ul>

            <div class="d-flex pt-3 justify-content-end">

              <a href="{{route("services.edit",["service"=> $service->id ])}}"
                class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>

              <a href="{{route("services.index")}}"
                class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection