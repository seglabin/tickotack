<!DOCTYPE html>
<html dir="ltr" lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Global Vision')</title>

  {{-- Aide les chemins relatifs du template --}}
  <base href="{{ url('/') }}/" />

  {{-- Fonts : preconnect pour fiabilité --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

  {{-- Font Awesome 5 (version locale fonctionnelle) + shims FA4 pour compatibilité classes fa fa-* --}}
  <link href="{{ asset('assets/templeApexa/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/templeApexa/plugins/fontawesome-free/css/v4-shims.min.css') }}" rel="stylesheet">

  {{-- Bootstrap CSS --}}
  <link href="{{ asset('templateFront/catalog/view/javascript/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" media="screen" />

  {{-- Theme CSS --}}
  <link href="{{ asset('templateFront/catalog/view/theme/default/stylesheet/stylesheet.css') }}" rel="stylesheet">

  {{-- Swiper CSS --}}
  <link href="{{ asset('templateFront/catalog/view/javascript/jquery/swiper/css/swiper.min.css') }}" type="text/css" rel="stylesheet" media="screen" />
  <link href="{{ asset('templateFront/catalog/view/javascript/jquery/swiper/css/opencart.css') }}" type="text/css" rel="stylesheet" media="screen" />

  {{-- Owl Carousel CSS (une seule version) --}}
  <link href="{{ asset('templateFront/catalog/view/javascript/jquery/swiper/css/owl.carousel.css') }}" type="text/css" rel="stylesheet" media="screen" />
  <link href="{{ asset('templateFront/catalog/view/javascript/jquery/swiper/css/owl.theme.css') }}" type="text/css" rel="stylesheet" media="screen" />

  {{-- Magnific Popup CSS --}}
  <link href="{{ asset('templateFront/catalog/view/javascript/jquery/magnific/magnific-popup.css') }}" type="text/css" rel="stylesheet" media="screen" />

  {{-- Newsletter CSS --}}
  <link href="{{ asset('templateFront/catalog/view/theme/default/stylesheet/inspirenewsletter.css') }}" type="text/css" rel="stylesheet" media="screen" />

  {{-- Core JS must be available before inline scripts in included views --}}
  <script src="{{ asset('templateFront/catalog/view/javascript/jquery/jquery-2.1.1.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('templateFront/catalog/view/javascript/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('templateFront/catalog/view/javascript/jquery/swiper/js/swiper.jquery.js') }}" type="text/javascript"></script>
  <script src="{{ asset('templateFront/catalog/view/javascript/jquery/swiper/js/owl.carousel.min.js') }}" type="text/javascript"></script>

  @stack('head')
</head>

<body>

  @include('partials.body_header')

  @yield('content')

  @include('partials.footer')

  {{-- Lightbox --}}
  <script src="{{ asset('templateFront/catalog/view/javascript/blog/lightbox-2.6.min.js') }}" type="text/javascript"></script>

  {{-- Countdown --}}
  <script src="{{ asset('templateFront/catalog/view/javascript/countdown/jquery.plugin.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('templateFront/catalog/view/javascript/countdown/jquery.countdown.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('templateFront/catalog/view/javascript/inspire/option.js') }}" type="text/javascript"></script>

  {{-- Carousels / Popups --}}
  <script src="{{ asset('templateFront/catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js') }}" type="text/javascript"></script>

  {{-- Zoom produit --}}
  <script src="{{ asset('templateFront/catalog/view/javascript/inspire/product-slider-zoom/jquery.elevatezoom.js') }}" type="text/javascript"></script>

  {{-- Common (comportements OpenCart du template) --}}
  <script src="{{ asset('templateFront/catalog/view/javascript/common.js') }}" type="text/javascript"></script>

  @stack('scripts')
</body>
</html>
