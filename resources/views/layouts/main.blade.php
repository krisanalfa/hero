<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

  <title>@yield('title', 'Hero')</title>

  <link rel="stylesheet" href="{{ URL::asset('/css/app.bundle.css') }}">

  <script type="text/javascript" src="{{ URL::asset('/js/head.bundle.js') }}"></script>

  @stack('styles')
</head>
<body>
  @yield('content')

  <script type="text/javascript" src="{{ URL::asset('/js/app.bundle.js') }}"></script>

  @stack('scripts')
</body>
</html>
