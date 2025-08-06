@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Company /</span> Videos</h4>

  @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  {{-- Upload Form --}}
  <div class="card mb-4">
    <div class="card-header">Upload Videos</div>
    <div class="card-body">
      <form method="POST" action="{{ route('company.videos.upload') }}" enctype="multipart/form-data">
        @csrf

        <div class="row align-items-end">
        <div class="col-md-6">
          <label for="videos" class="form-label">Select Videos</label>
          <input type="file" name="videos[]" class="form-control" accept="video/*" multiple>
        </div>
        <div class="col-md-3 mt-4 mt-md-0">
          <button type="submit" class="btn btn-primary">Upload</button>
           </div>
      </div>
      </form>
    </div>
  </div>

  {{-- Video Gallery --}}
  <div class="card">
    <div class="card-header">Uploaded Videos</div>
    <div class="card-body">
      @if ($company->videos->count())
        <div class="row">
          @foreach ($company->videos as $video)
            <div class="col-md-6 mb-4">
              <div class="border rounded p-2 text-center">
                {{-- Clickable thumbnail to open modal --}}
                {{-- <a href="#videoModal" class="video-popup" data-bs-toggle="modal" data-bs-target="#videoModal" data-src="{{ asset('storage/' . $video->file_name) }}">
                  <video width="100%" height="auto" muted>
                    <source src="{{ asset('storage/' . $video->file_name) }}" type="video/mp4">
                  </video>
                </a> --}}
                <a href="#videoModal" class="video-popup" data-bs-toggle="modal" data-bs-target="#videoModal" data-src="{{ asset('storage/' . $video->file_name) }}">
                    <video width="100%" height="auto" controls preload="metadata">
                    <source src="{{ asset('storage/' . $video->file_name) }}" type="video/mp4">
                   Your browser does not support the video tag.
                   </video>
                </a>

                <p class="small text-muted text-break mt-2">{{ basename($video->file_name) }}</p>

                {{-- Delete Button --}}
                <form method="POST" action="{{ route('company.videos.delete', $video->id) }}" onsubmit="return confirm('Are you sure you want to delete this video?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-muted">No videos uploaded yet.</p>
      @endif
    </div>
  </div>
</div>

<!-- Video Preview Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Video Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-0 text-center">
        {{-- <video id="modalVideo" controls style="width: 100%; height: auto;"> --}}
            <video id="modalVideo" controls muted style="width: 100%; height: auto;">

          <source id="modalSource" src="" type="video/mp4">
          Your browser does not support HTML5 video.
        </video>
      </div>

      <div class="d-flex justify-content-between px-3 pb-3">
        <button class="btn btn-outline-secondary" onclick="navigateVideo('prev')">← Previous</button>
        <button class="btn btn-outline-secondary" onclick="navigateVideo('next')">Next →</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const modalVideo = document.getElementById('modalVideo');
    const videoSource = document.getElementById('modalSource');
    const videoModal = document.getElementById('videoModal');

    let videoUrls = [];
    let currentIndex = 0;

    // Collect video URLs from thumbnails
    const videoLinks = Array.from(document.querySelectorAll('.video-popup'));
    videoUrls = videoLinks.map(link => link.getAttribute('data-src'));

    videoLinks.forEach((link, index) => {
      link.addEventListener('click', function () {
        currentIndex = index;
        loadVideo(videoUrls[currentIndex]);
      });
    });

    // function loadVideo(url) {
    //   videoSource.src = url;
    //   modalVideo.load();
    //   setTimeout(() => {
    //     modalVideo.play().catch(err => {
    //       console.warn("Autoplay blocked:", err);
    //     });
    //   }, 200);
    // }
    function loadVideo(url) {
  videoSource.src = url;
  modalVideo.load();

  // Wait until metadata is loaded before trying to play
  modalVideo.onloadedmetadata = function () {
    modalVideo.play().catch(err => {
      console.warn("Autoplay blocked:", err);
    });
  };
}


    videoModal.addEventListener('hidden.bs.modal', function () {
      modalVideo.pause();
      modalVideo.currentTime = 0;
      videoSource.src = '';
      modalVideo.load();
    });

    window.navigateVideo = function(direction) {
      if (direction === 'next') {
        currentIndex = (currentIndex + 1) % videoUrls.length;
      } else {
        currentIndex = (currentIndex - 1 + videoUrls.length) % videoUrls.length;
      }
      loadVideo(videoUrls[currentIndex]);
    };

    // Optional: Arrow key navigation
    document.addEventListener('keydown', function (e) {
      if (!videoModal.classList.contains('show')) return;

      if (e.key === 'ArrowRight') {
        window.navigateVideo('next');
      } else if (e.key === 'ArrowLeft') {
        window.navigateVideo('prev');
      }
    });
  });
</script>
@endpush
