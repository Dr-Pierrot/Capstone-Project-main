  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <!-- Additional JS plugins -->
  <!-- Ensure these scripts are correctly referenced -->
  <script src="{{ asset('assets/js/vartical-layout.min.js') }}"></script>
  <script src="{{ asset('assets/js/script.js') }}"></script>
  <script src="{{ asset('assets/pages/accordion/accordion.js') }}"></script>

  <!-- Initialization Scripts -->
  <script>
      $(document).ready(function() {
          $('.pcoded-navbar').pcodedmenu(); // Ensure pcodedmenu plugin is loaded
          $('.accordion').accordion(); // Ensure accordion plugin is loaded
          $('[data-toggle="tooltip"]').tooltip(); // Ensure tooltip plugin is loaded
      });
  </script>