
<style>
    .alert { margin-top: .5rem !important; margin-bottom: .5rem !important; padding: .5rem .75rem !important; }
.alert p:last-child { margin-bottom: 0 !important; }
.alert:last-child { margin-bottom: 0 !important; }
</style>
@if ($errors->any())
<div class="container-xxl mt-1">
    <div class="alert alert-danger">

        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="container-xxl mt-1">
@if(Session::has('success'))
  <div class="alert alert-success alert-dismissible fade show mt-2 mb-2" role="alert">
    {{ Session::get('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if(Session::has('error'))
  <div class="alert alert-danger alert-dismissible fade show mt-2 mb-2" role="alert">
    {{ Session::get('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
</div>

