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


   @if(Session::has('success'))
   <div class="container-xxl mt-1">
    <div class="card-body pt-0">
      <div class="alert alert-success">
      {{ Session::get('success') }}
      </div>
    </div>
    </div>
  @endif
  @if(Session::has('error'))
   <div class="container-xxl mt-1">
    <div class="card-body pt-0">
      <div class="alert alert-danger">
      {{ Session::get('error') }}
      </div>
    </div> 
    </div> 
  @endif

