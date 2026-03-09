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

    <section class="dynamic-content-section m-4">
        <div class="dynamic-rich-text">
            {!! $nav->content !!}
        </div>
    </section>

    @include('partials_new.footer')

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/frontend/js/script_new.js"></script>
</body>

</html>
