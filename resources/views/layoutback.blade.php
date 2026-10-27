<!DOCTYPE html>
<html class="no-js" lang="fr">
	
<!-- Mirrored from apexa-html-demo.vercel.app/ by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 07 Sep 2024 20:32:16 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="ie=edge" />
		<title>SIGAF - Système Informatique de Gestion Améliorée des Finances</title>
		<meta name="description" content="SIGAF - Système Informatique de Gestion Améliorée des Finances" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Inclure le Token CSRF  -->
		<base href="{{ url('/') }}/" />
		
		<link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/templeApexa/img/favicon.png')}}" />
		<!-- Place favicon.ico in the root directory -->
		<!-- CSS here -->
		    @include('back.includes.css')
	</head>
	<body class="back-office">
        <!--Preloader-->
         @include('back.includes.preloader')
     	<!--Preloader-end -->
		<!-- Scroll-top -->
		<button class="scroll__top scroll-to-target" data-target="html">
			<i class="fas fa-angle-up"></i>
		</button>
		<!-- Scroll-top-end-->
		<!-- header-area -->
		<header class="transparent-header">
		 @include('back.includes.topheader')	
			<div id="sticky-header" class="tg-header__area">
				<div class="container custom-container">
					<div class="row">
                         @include('back.includes.menu')
					</div>
				</div>
			</div>
			<!-- header-search -->
              @include('back.includes.headerSearch')
			<!-- header-search-end -->
			<!-- offCanvas-menu -->
              @include('back.includes.offcanvas')
			<!-- offCanvas-menu-end -->
		</header>
		<!-- header-area-end -->
		<!-- main-area -->
		<main class="fix">
			@php
				$idenreg = (session('idenreg') != null) ? session('idenreg') : null;
				$config = (session('config') != null) ? session('config') : null;
				if(!isset($titre)) $titre = null;
				if(!isset($nouv)) $nouv = null;
				if(!isset($retour)) $retour = null;
			@endphp

			
			<form action = "" name="myform" id="myform" method="get" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="idenreg" name="idenreg" value="{{ $idenreg}}">
                <input type="hidden" id="config" name="config" value="{{ $config}}">
				<section class="about__area-five" >
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="about__content-five">
								<div class="section-title mb-30">
									<div class="row"> 
										<div class="col-md-10"><h2 class="title">{{$titre}}</h2></div>
										<div class="col-md-2">
											@if ($nouv != null)
											<!-- <button type="button" class="btn btnarrondi m-b-10 btn-outline-primary"
												onclick="nouveau('{{$nouv}}');"><i class="fa fa-plus-circle"></i>
												&nbsp; Nouveau</button> -->

										@endif
										@if ($retour != null)
											<!-- <button type="button" class="btn btnarrondi m-b-10 btn-outline-primary"
												onclick="afficher('{{$retour}}');"><i class="fa fa-arrow-left"></i>
												&nbsp; Retour</button> -->											

										@endif
										</div>

									</div>

									@if ($nouv != null || $retour != null)
									<div class="back-mobile-actions d-md-none" role="group" aria-label="Actions de la page">
										@if ($retour != null)
										<a class="btn btn-outline-secondary" href="{{ url(ltrim((string) $retour, '/')) }}">
											<i class="fa fa-arrow-left" aria-hidden="true"></i> Retour
										</a>
										@endif
										@if ($nouv != null)
										<a class="btn btn-primary" href="{{ url(ltrim((string) $nouv, '/')) }}">
											<i class="fa fa-plus-circle" aria-hidden="true"></i> Nouveau
										</a>
										@endif
									</div>
									@endif
									
								</div>

								@yield('content')
							</div>
						</div>
					</div>
				</div>
        </section>				

            </form>

		</main>
		<!-- main-area-end -->
		<!-- footer-area -->
		<footer>
             @include('back.includes.footer')
		</footer>
		<!-- footer-area-end -->
		<!-- JS here -->
          @include('back.includes.js')
		  @yield('modals')
		  @auth
		  	@include('auth.modalChangePassword')
			@if(session('config') == 'utilisateur')
				@include('back.modalResetPassword')
			@endif
		  @endauth
        <script>
            (function () {
                function hidePreloader() {
                    var preloader = document.getElementById('preloader');
                    if (preloader) {
                        preloader.style.display = 'none';
                        preloader.setAttribute('aria-hidden', 'true');
                    }
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', hidePreloader, { once: true });
                } else {
                    hidePreloader();
                }

                window.addEventListener('pageshow', hidePreloader);
            })();
        </script>
	</body>

<!-- Mirrored from apexa-html-demo.vercel.app/ by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 07 Sep 2024 20:34:46 GMT -->
</html>
