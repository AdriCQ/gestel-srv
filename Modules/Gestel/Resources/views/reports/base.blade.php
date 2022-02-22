<!DOCTYPE html>
<html>
<head>
  <title>Reportes</title>

  <link rel="stylesheet" type="text/css" href="{{ asset('assets/bootstrap-5.1.3-dist/css/bootstrap.min.css') }}">

  <style>
    .container {
      padding: 1rem;
    }
  </style>
</head>
<body>
  <div class="container">
    @yield('body')
  </div>

  <script src="{{ asset('assets/bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>