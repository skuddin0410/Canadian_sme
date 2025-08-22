<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

      <title>@yield('title')</title>

      <meta name="description" content="" />

      <!-- Favicon -->
      <link rel="icon" type="image/x-icon" href="{{asset('backend/assets/img/favicon/favicon.ico')}}" />
      <!-- Lightbox2 CSS -->
      <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">



      <!-- Fonts -->
      <link rel="preconnect" href="https://fonts.googleapis.com" />
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
      <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />
     
      <link rel="stylesheet" href="{{asset('backend/assets/vendor/fonts/boxicons.css')}}" />
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
      <!-- Core CSS -->
      <link rel="stylesheet" href="{{asset('backend/assets/vendor/css/core.css')}}" class="template-customizer-core-css" />
      <link rel="stylesheet" href="{{asset('backend/assets/vendor/css/theme-default.css')}}" class="template-customizer-theme-css" />
      <link rel="stylesheet" href="{{asset('backend/assets/css/demo.css')}}" />

      <!-- Vendors CSS -->
      <link rel="stylesheet" href="{{asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
      <link rel="stylesheet" href="{{asset('backend/assets/vendor/libs/apex-charts/apex-charts.css')}}" />
      <!-- Alertify CSS -->
      <link href="{{asset('backend/plugins/alertify/alertify.css?v='.time())}}" rel="stylesheet" />
      <link rel="stylesheet" href="{{asset('backend/assets/css/custom.css')}}" />
      <link href="{{asset('backend/plugins/datatable/fixedColumns.dataTables.min.css?v='.time())}}" rel="stylesheet" />
      <script src="{{asset('backend/assets/vendor/js/helpers.js')}}"></script>
      <script src="{{asset('backend/assets/js/config.js')}}"></script>

      <script src="https://cdn.tiny.cloud/1/svsicmdojxq1j88fchr7d66cbfndgngknh06cgapmgeskqsl/tinymce/6/tinymce.min.js" ></script>

  </head>

  <body>
    <!-- Layout wrapper -->
  
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        @if(Auth::user()->hasRole('Admin') )
            @include('partial.admin-side-bar')
            {{-- @include('partial.exhibitor-side-bar') --}}
        @endif    
        
        @if(Auth::user()->hasRole('Registration Desk'))
           @include('partial.registration-desk-sidebar')
        @endif 

        @if(Auth::user()->hasRole('Support Staff Or Helpdesk'))
           @include('partial.support-staff-side-bar')
        @endif 

        <!-- / Menu -->
        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
              @include('partial.nav-bar')


          <div class="content-wrapper">
            @include('partial.error')
            @yield('content')
    
          @include('partial.footer')
          <div class="content-backdrop fade"></div>
          </div>
        </div>
        </div>

      <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    @include('partial.script')
      @yield('scripts')
      <!-- Lightbox2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script type="text/javascript">
 tinymce.init({
    selector: '#description',
    readonly: false,
    width: '100%',
    height: 300,
    plugins: 'code image link lists table preview',
    toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | media| code preview',
    menubar: false,
    branding: false,

    
    
  });
</script>
  </body>
</html>
<script type="text/javascript">
 tinymce.init({
    selector: '#description',
    readonly: false,
    width: '100%',
    height: 300,
    plugins: 'code image link lists table preview',
    toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | media| code preview',
    menubar: false,
    branding: false,
  });

  tinymce.init({
    selector: '.editor2',
    readonly: false,
    width: '100%',
    height: 300,
    plugins: 'code image link lists table preview',
    toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | media| code preview',
    menubar: false,
    branding: false,
  });
</script>

<script>
document.getElementById("profileImageInput").addEventListener("change", function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("profileImagePreview").src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>