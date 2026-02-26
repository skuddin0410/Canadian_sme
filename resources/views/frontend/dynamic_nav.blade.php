<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $nav->title }} | {{ config('app.name') }}</title>
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <!-- css -->
    <link rel="stylesheet" href="/frontend/css/style_new.css">
    <link rel="stylesheet" href="/frontend/css/developer.css">
</head>

<body>

    @include('partials_new.header')

    <section class="page-header">
        <div class="container">
            <h1>{{ $nav->title }}</h1>
            <div class="breadcrumb-custom">
                <a href="{{ url('/') }}">Home</a>
                <span>/</span>
                <span>{{ $nav->title }}</span>
            </div>
        </div>
    </section>

    <section class="dynamic-content-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-card">
                        <div class="dynamic-rich-text">
                            {!! $nav->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials_new.footer')

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/frontend/js/script_new.js"></script>
</body>

</html>
