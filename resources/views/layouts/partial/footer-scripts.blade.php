 
 
<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS  
<script src="{{ asset('plugins/chart.js/Chart.min.js')}}"></script>-->
<!-- Sparkline 
<script src="{{ asset('plugins/sparklines/sparkline.js')}}"></script>--
<!-- JQVMap  
<script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa')}}.js"></script>
 jQuery Knob Chart -->
<script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker 
<script src="{{ asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>-->
<!-- Tempusdominus Bootstrap 4  
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>-->
<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.js')}}"></script>
<!-- AdminLTE for demo purposes  
<script src="{{ asset('dist/js/demo.js')}}"></script>-->
<!-- AdminLTE dashboard demo (This is only for demo purposes)  
<script src="{{ asset('dist/js/pages/dashboard.js')}}"></script>
-->
<script>
    const baseUrl = document.querySelector('meta[name="app-url"]').getAttribute('content');
    function ConfirmDelete()
    {
      return confirm("Êtes vous sûres ?");
    }
</script>
<!-- toast alert--->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
            @if(Session::has('success'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
                    toastr.success("{{ session('success') }}");
            @endif

            @if(Session::has('error'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
                    toastr.error("{{ session('error') }}");
            @endif

            @if(Session::has('info'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
                    toastr.info("{{ session('info') }}");
            @endif

            @if(Session::has('warning'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
                    toastr.warning("{{ session('warning') }}");
            @endif

    $('.card-header').css('cursor', 'pointer').on('click', function(e) {
        // Vérifier si l'élément cliqué ou un de ses parents est un bouton
        if (!$(e.target).closest('.btn').length) {
            $(this).next('.card-body').slideToggle();
        }
    });
    
    // Option: Ajouter une petite icône pour indiquer que c'est cliquable
    $('.card-header').append('<i class="fas fa-chevron-down float-end"></i>');
    $('.card-header').on('click', function() {
        $(this).find('.fa-chevron-down, .fa-chevron-up').toggleClass('fa-chevron-down fa-chevron-up');
    });
</script>

@yield('footer-scripts')
