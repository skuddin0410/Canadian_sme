@extends('layouts.admin')

@section('content')
<div class="container-xxl container-p-y">
  <h4 class="mb-4"><span class="text-muted fw-light">Marketing /</span> View Material</h4>

  <div class="card">
    <div class="card-body">
    <div class="d-flex pt-3 justify-content-end">
      <a href="{{route("trainings.edit",["training"=> $training->id ])}}"
        class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>
      <a href="{{route("trainings.index")}}"
        class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
    </div>
      
      <h5 class="pb-2 border-bottom mb-4">Traing Details</h5>
      <h5>{{ $training->material_name }}</h5>

      <p class="mb-3">{!! $training->material_description !!}</p>

      @if ($training->youtube_link)
        <p><strong>YouTube:</strong> <a href="{{ $training->youtube_link }}" target="_blank">{{ $training->youtube_link }}</a></p>
      @endif

      @if($training->material && $training->material->file_name)
        <p><strong>File:</strong> <a href="{{$training->material->file_path  }}" target="_blank"><i class="fa fa-file"></i></a> </p>
      @endif

      <p><small><strong>Uploaded on:</strong> {{ $training->created_at->format('d M Y') }}</small></p>

      <div class="d-flex pt-3 justify-content-end">

        <a href="{{route("trainings.edit",["training"=> $training->id ])}}"
          class="btn btn-outline-primary btn-pill btn-streach font-book me-2 mt-6 fs-14 ">Edit</a>

        <a href="{{route("trainings.index")}}"
          class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14">Back</a>
      </div>
    </div>
  </div>
</div>
@endsection
