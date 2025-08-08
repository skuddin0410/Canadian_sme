@extends('layouts.admin')

@section('title')
Admin | Product Details
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Product/</span>Page</h4>
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

            <a href="{{route("products.edit",["product"=> $product->id ])}}"
              class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>

            <a href="{{route("products.index")}}"
              class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
          </div>
          <h5 class="pb-2 border-bottom mb-4">CMS Page Details</h5>
          <div class="info-container">
            <ul class="list-unstyled">
              <li class="mb-3">
                <span class="fw-medium me-2">Title:</span>
                <span>{{ $product->name }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Price:</span>
                <span>{{config('app.currency_sign')}} {{ $product->price }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Description:</span>
                <span>{!! $product->description !!}</span>
              </li>
              <li class="mb-3">
                <span class="fw-medium me-2">Image:</span>
                @if(!empty($product->image_url) )
                <div class="d-flex flex-wrap gap-2">
                  <span class="fw-medium me-2"><img src="{{ asset('storage/' . $product->image_url) }}"
                      alt="{{ $product->name }}" style="width: 80px; height: 80px; object-fit: cover;"
                      class="rounded border"></span>
                </div>
                @endif
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Gallery images:</span>
                @if($product->gallery_images)
                <div class="d-flex flex-wrap gap-2">
                  @foreach($product->gallery_images as $img)
                  <span class="fw-medium me-2">
                    <img src="{{ asset('storage/' . $img) }}" style="width: 80px; height: 80px; object-fit: cover;"
                      class="rounded border">
                    @endforeach
                </div>
                @endif
              <li>

              <li class="mb-3">
                <span class="fw-medium me-2">Features:</span>
                <span>{!! $product->features ?? '' !!}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Benefits:</span>
                <span>{!! $product->benefits ?? '' !!}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Category:</span>
                <span>{{ $product->category->name ?? 'Uncategorized' }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Status:</span>
                <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                  {{ $product->is_active ? 'Active' : 'Inactive' }}
                </span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Sort Order:</span>
                <span>
                  {{ $product->sort_order }}
                </span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Meta Title:</span>
                <span>{{ $product->meta_title }}</span>
              </li>
              <li class="mb-3">
                <span class="fw-medium me-2">Meta Description:</span>
                <span>{!! $product->meta_description !!}</span>
              </li>
              <li class="mb-3">
                <span class="fw-medium me-2">Meta Keywords:</span>
                <span>{{ $product->meta_keywords }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Created by:</span>
                <span>{{ $product->creator->full_name ?? 'System' }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Updated by:</span>
                <span>{{ $product->updater->full_name ?? '—' }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Created at:</span>
                <span>{{ $product->created_at->format('M d, Y H:i') }}</span>
              </li>

              <li class="mb-3">
                <span class="fw-medium me-2">Last updated:</span>
                <span>{{ $product->updated_at->format('M d, Y H:i') }}</span>
              </li>
            </ul>

            <div class="d-flex pt-3 justify-content-end">

              <a href="{{route("products.edit",["product"=> $product->id ])}}"
                class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>

              <a href="{{route("products.index")}}"
                class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection