<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- <link rel="icon" href="<%= BASE_URL %>favicon.ico"> -->

  <title>Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template</title>

  <!-- Splash Screen/Loader Styles -->
  <link rel="stylesheet" type="text/css" href="{{ asset(mix('css/loader.css')) }}" />

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">

  <!-- Favicon -->
  <link rel="shortcut icon" href="{{ asset('images/logo/favicon.png') }}">

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap"
    rel="stylesheet">
</head>

<body>
  <noscript>
    <strong>We're sorry but Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template doesn't work properly without
      JavaScript enabled. Please enable it to continue.</strong>
  </noscript>
  <div id="loading-bg">
    <div class="loading-logo">
      <img src="{{ asset('logo.png') }}" alt="Logo" />
    </div>
    <div class="loading">
      <div class="effect-1 effects"></div>
      <div class="effect-2 effects"></div>
      <div class="effect-3 effects"></div>
    </div>
  </div>
  <div id="app">
  </div>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="{{ asset(mix('js/app.js')) }}"></script>

</body>

</html>
