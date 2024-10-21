<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    @include('includes.style')
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

</head>
<body>

    <!-- ======= Header ======= -->
    @include('layouts.navbar')
    <!-- End Header -->
  
    <!-- ======= Sidebar ======= -->
    @include('layouts.sidebar')
    <main id="main" class="main">
  
      <section class="section">
        @yield('content')
      </section>
  
    </main><!-- End #main -->
  
    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
      <div class="copyright">
        &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
      </div>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
    </footer><!-- End Footer -->
    
    @include('includes.script')

</body>
</html>
