@if ($errors->any())
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <div class="alert alert-danger">

        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
