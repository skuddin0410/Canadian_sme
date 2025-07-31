<script src="{{asset('backend/assets/vendor/libs/jquery/jquery.js')}}"></script>
<script src="{{asset('backend/assets/vendor/libs/popper/popper.js')}}"></script>
<script src="{{asset('backend/assets/vendor/js/bootstrap.js')}}"></script>
<script src="{{asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
<script src="{{asset('backend/assets/vendor/js/menu.js')}}"></script>
<script src="{{asset('backend/plugins/alertify/alertify.js')}}"></script>
<script src="{{asset('backend/plugins/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backend/plugins/datatable/dataTables.bootstrap4.min.js')}}"></script>

<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{asset('backend/assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>

<!-- Main JS -->
<script src="{{asset('backend/assets/js/main.js')}}"></script>

<!-- Page JS -->
<script src="{{asset('backend/assets/js/dashboards-analytics.js')}}"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

<script type="text/javascript">
    var base_url = '{{ url('') }}/'; 
    var csrf_token = "{{csrf_token()}}"; 
    alertify.set('notifier','position', 'top-right');
    $(document).ready(function () {
        $("form").attr("autocomplete", "off");
        setTimeout(function(){
            //$('.dataTables_processing').removeClass('card');
            $('.dataTables_filter input.form-control-sm').removeClass('form-control-sm');
        }, 10)
    })
</script>

