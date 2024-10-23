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
  
    
    @include('includes.script')

</body>
</html>
