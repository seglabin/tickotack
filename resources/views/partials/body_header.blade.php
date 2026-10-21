<!-- <body> -->
<script src="{{ asset('templateFront/js/fonction.js') }}"></script>

<style>
  .imgpanier {
    border-radius: 2px;
    width: 40px;
    height: 40px;
  }
</style>
@php
use Illuminate\Support\Facades\DB;

$repertCat = "storage/images/Categories/";
$rekcateg = " SELECT c.*, id AS slug ";
$rekcateg .= " , COALESCE((SELECT COUNT(a.id) FROM articles a WHERE idcategorie = c.id GROUP BY idcategorie), 0) nbprod ";
$rekcateg .= " FROM categories c WHERE visible = true ORDER BY libelle ";
$navCategories = collect(DB::select($rekcateg));

$q = request()->query('q', '');
$cat = request()->query('cat', ''); // slug catégorie pour la recherche

// Panier
$panier = session('panier') ?? [];
$colmont = array_column($panier, 'montant');
$totalpanier = array_sum($colmont);
$nbre = count($panier);
@endphp

<form id="formAddCart" method="GET" enctype="multipart/form-data">
  <input id="idproduitAjout" name="idproduitAjout" type="hidden" value="">
  <input id="qteAjout" name="qteAjout" type="hidden" value="1">

</form>

<nav id="top">
  <div class="container">
    <div class="row">
      <div class="col-sm-5 col-lg-5 col-md-4 col-xs-7 header-social">
        <div class="header-social">
          <div class="socials-block">
            <ul>
              <li><a href="#" rel="nofollow"><i class="fa fa-twitter"></i></a></li>
              <li><a href="#" rel="nofollow"><i class="fa fa-facebook"></i></a></li>
              <li><a href="#" rel="nofollow"><i class="fa fa-google-plus"></i></a></li>
              <li><a href="#" rel="nofollow"><i class="fa fa-skype"></i></a></li>
              <li><a href="#" rel="nofollow"><i class="fa fa-pinterest-p"></i></a></li>
              <li><a href="#" rel="nofollow"><i class="fa fa-youtube-play"></i></a></li>
              <li><a href="#" rel="nofollow"><i class="fa fa-linkedin"></i></a></li>
            </ul>
          </div>
        </div>
      </div>

      <div id="top-links" class="col-sm-7 col-lg-7 col-md-8 col-xs-5 text-right">
        <ul class="list-inline pull-right">
          <!-- <li class="hidden-sm hidden-xs">
            <b>COUPON :</b> GROCERY10OFF
          </li> -->

          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="My Account">
              <i class="fa fa-user hidden-md hidden-lg hidden-sm"></i>
              <span class="hidden-xs">My Account</span>&nbsp;<i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right userdown">
              <li><a href="{{ route('register') }}"><i class="fa fa-address-book-o"></i> Register</a></li>
              <li><a href="{{ route('login') }}"><i class="fa fa-sign-in"></i> Login</a></li>
              <li><a href="#" onclick="return false;"><i class="fa fa-heart"></i> <span>Wish List (0)</span></a></li>
            </ul>
          </li>

          {{-- Currency (UI only) --}}
          <li class="xs-cur">
            <div class="btn-group">
              <button class="btn btn-link dropdown-toggle" data-toggle="dropdown">
                <strong>$</strong> <span class="hidden-xs">Currency</span>&nbsp;<i class="fa fa-angle-down"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-right">
                <li><button class="btn btn-link btn-block" type="button" onclick="return false;">€ Euro</button></li>
                <li><button class="btn btn-link btn-block" type="button" onclick="return false;">₹ Indian Rupee</button></li>
                <li><button class="btn btn-link btn-block" type="button" onclick="return false;">£ Pound Sterling</button></li>
                <li><button class="btn btn-link btn-block" type="button" onclick="return false;">$ US Dollar</button></li>
              </ul>
            </div>
          </li>

          {{-- Language (UI only) --}}
          <li>
            <div class="btn-group">
              <button class="btn btn-link dropdown-toggle" data-toggle="dropdown">
                <img src="{{ asset('templateFront/catalog/language/fr-fr/fr-fr.png') }}" alt="French" title="French">
                <span class="hidden-xs">Language</span>&nbsp;<i class="fa fa-angle-down"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-right">
                <li><button class="btn btn-link btn-block" type="button" onclick="return false;"><img src="{{ asset('templateFront/catalog/language/fr-fr/fr-fr.png') }}" alt="French"> French</button></li>
                <li><button class="btn btn-link btn-block" type="button" onclick="return false;"><img src="{{ asset('templateFront/catalog/language/en-gb/en-gb.png') }}" alt="English"> English</button></li>
                <li><button class="btn btn-link btn-block" type="button" onclick="return false;"><img src="{{ asset('templateFront/catalog/language/es-es/es-es.png') }}" alt="Spanish"> Spanish</button></li>
              </ul>
            </div>
          </li>

        </ul>
      </div>
    </div>
  </div>
</nav>

<header>
  <div class="container">
    <div class="row">

      <div class="col-sm-3 col-md-3">
        <div id="logo">
          <a href="{{ route('home') }}">
            <span class="home-brand-logo" aria-label="Global Vision">
              <i class="fa fa-leaf" aria-hidden="true"></i> Global Vision
            </span>
          </a>
        </div>
      </div>

      {{-- Search --}}
      <div class="col-lg-5 col-md-5 co-sm-5 col-xs-12 xsse">
        <div id="search-by-category">
          <div class="search-container">
            <div class="categories-container">
              <div class="hover-cate">
                <p>
                  <span class="cate-selected" data-value="{{ $cat ?: 'all' }}">
                    {{ $cat ? (collect($navCategories)->firstWhere('slug', $cat)['name'] ?? 'Toutes les Categories') : 'Toutes les Categories' }}
                  </span>
                </p>
                <ul class="cate-items" style="display:none;">
                  <li class="item-cate {{ $cat=='' ? 'selected' : '' }}" data-value="all">Toutes les Categories</li>
                  @foreach($navCategories as $c)
                  <li class="item-cate {{ $cat==$c->id ? 'selected' : '' }}" data-value="{{ $c->id }}">{{ $c->libelle }}</li>
                  @endforeach
                </ul>
              </div>
            </div>

            <form class="search-box input-group" action="{{ route('search') }}" method="GET">
              <input type="hidden" name="cat" id="search-cat" value="{{ $cat }}">
              <input type="text" name="q" id="text-search" value="{{ $q }}" placeholder="Search" class="">
              <div id="sp-btn-search" class="input-group-btn">
                <button type="submit" id="btn-search-category" class="btn"><i class="fa fa-search"></i></button>
              </div>
            </form>

          </div>

          {{-- Container AJAX template (maquette) --}}
          <div class="search-ajax">
            <div class="ajax-loader-container" style="display:none;">
              <i class="fa fa-spinner fa-spin ajax-load-img" aria-hidden="true"></i>
            </div>
            <div class="ajax-result-container"></div>
          </div>

          <input type="hidden" id="ajax-search-enable" value="0">
        </div>

        <script>
          $(document).ready(function() {
            var flag = false;

            $('.hover-cate p').click(function() {
              $(".cate-items").slideToggle("slow");
              $(".cate-items").addClass("sblock");
            });

            $('.ajax-result-container').hover(
              function() {
                flag = true;
              },
              function() {
                flag = false;
              }
            );

            $('.hover-cate').hover(
              function() {
                flag = true;
              },
              function() {
                flag = false;
              }
            );

            $('#search-by-category').focusout(function() {
              if (flag === true) $('.ajax-result-container').show();
              else $('.ajax-result-container').hide();
            });

            $('#search-by-category').focusin(function() {
              $('.ajax-result-container').show();
            });

            $('ul.cate-items li.item-cate').click(function() {
              var v = $(this).data('value');
              var label = $(this).html();

              $('.cate-selected').attr('data-value', v).html(label);
              $('#search-cat').val(v === 'all' ? '' : v);

              $(".cate-items").hide();
              $('#text-search').focus();
            });
          });
        </script>
      </div>

      {{-- Call + Cart --}}
      <div class="col-lg-4 col-md-4 col-sm-4 text-right xs-cart">
        <a href="{{ route('contact') }}" class="hidden-sm hidden-xs hidden-md">
          <img src="{{ asset('templateFront/image/catalog/icon-call.png') }}" alt="">
          <div class="cartco">
            <span>Contacter nous :</span><br>
            <span id="contact-phone">123456789</span>
          </div>
        </a>

        <div id="cart" class="btn-group">
          <button data-toggle="dropdown" data-loading-text="Loading..." class="dropdown-toggle">
            <img src="{{ asset('templateFront/image/catalog/carti.png') }}" alt="">
            <div class="cartco">
              <span>Mon panier</span><br>
              <span id="cart-total"><span class="cartcount">{{$nbre}}</span>{{formatMontant($totalpanier)}}</span>
            </div>
          </button>
          <ul class="dropdown-menu pull-right">
            <li>
              <p class="text-center">
                @if($nbre > 0)
              <div class="table-responsive">
                <table class="table table-bordered ">
                  <thead>
                    <tr>
                      <th>img</th>
                      <th>Produit</th>
                      <th>Qte</th>
                      <th>PU</th>
                      <th>Montant</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                    $subtotal = 0;
                    @endphp
                    <!-- //idproduit   libelle photo   pu  qte montant -->
                    @foreach($panier as $p)

                    @php
                    $subtotal += $p['montant'];
                    @endphp
                    <tr>
                      <td> <img class="imgpanier img-responsive center-block" src="{{ asset($p['photo']) }}" alt="{{ $p['libelle'] }}"> </td>
                      <td>{{$p['libelle']}}</td>
                      <td>{{$p['qte']}}</td>
                      <td>{{$p['pu']}}</td>
                      <td>{{$p['montant']}}</td>

                    </tr>
                    @endforeach
                  </tbody>
                </table>
                @php
                $shipping = $subtotal > 0 ? 2.50 : 0;
                $tax = $subtotal > 0 ? round($subtotal * 0.18, 2) : 0;
                $total = $subtotal + $shipping + $tax;
                @endphp
                <div class="row">
                  <div class="col-sm-4"></div>
                  <div class="col-sm-8 ">
                    <table class="table table-bordered">
                      <tbody>
                        <tr>
                          <td class="text-right"><strong>Sous-total</strong></td>
                          <td class="text-right" id="cartSubtotal">{{ number_format($subtotal, 2, ',', ' ') }} €</td>
                        </tr>
                        <tr>
                          <td class="text-right"><strong>Livraison</strong></td>
                          <td class="text-right" id="cartShipping">{{ number_format($shipping, 2, ',', ' ') }} €</td>
                        </tr>
                        <tr>
                          <td class="text-right"><strong>TVA (simulée)</strong></td>
                          <td class="text-right" id="cartTax">{{ number_format($tax, 2, ',', ' ') }} €</td>
                        </tr>
                        <tr>
                          <td class="text-right"><strong>Total</strong></td>
                          <td class="text-right" id="cartTotal">{{ number_format($total, 2, ',', ' ') }} €</td>
                        </tr>
                      </tbody>

                    </table>
                  </div>
                </div>
              </div>


              @else
              Panier vide.
              @endif
              </p>
            </li>
            <li>
              <div style="padding:10px 15px;">
                <div class="row">
                  <div class="col-md-6">
                    <a class="btn btn-default btn-block" href="{{ route('cart') }}">Voir le panier</a>
                  </div>

                  <div class="col-md-6">
                    <a class="btn btn-primary btn-block" href="{{ route('checkout') }}">Commander</a>
                  </div>

                </div>
              </div>
            </li>
          </ul>
        </div>

      </div>

    </div>
  </div>
</header>

<style>
  @media (min-width: 768px) {
    .allmenu {
      height: auto;
      min-height: 50px;
    }

    .allmenu .container>.row {
      display: flex;
      align-items: center;
    }

    .allmenu .static-menu {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      flex-wrap: wrap;
      gap: 4px 8px;
      margin: 0;
      padding: 5px 0;
    }

    .allmenu .static-menu>li {
      display: flex;
      align-items: center;
      margin: 0;
      padding: 0;
    }

    .allmenu .static-menu>li>.front-menu-link {
      display: inline-flex;
      align-items: center;
      min-height: 40px;
      padding: 0 10px;
      border-radius: 8px;
      color: #fff;
      font-size: 14px;
      font-weight: 500;
      letter-spacing: 0.2px;
      text-decoration: none;
      text-transform: none;
      transition: background-color 0.2s ease, color 0.2s ease;
      white-space: nowrap;
    }

    .allmenu .static-menu>li>.front-menu-link:hover,
    .allmenu .static-menu>li>.front-menu-link:focus {
      background: rgba(255, 255, 255, 0.14);
      color: #ffd200;
    }

    .allmenu .static-menu .front-menu-back-button,
    .allmenu .static-menu .front-menu-login {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 38px;
      padding: 0 13px;
      border: 1px solid rgba(255, 255, 255, 0.8);
      border-radius: 20px;
      background: #fff;
      color: #347f12;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 0.2px;
      line-height: 1;
      text-decoration: none;
      transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
      white-space: nowrap;
    }

    .allmenu .static-menu .front-menu-back-button {
      cursor: pointer;
    }

    .allmenu .static-menu .front-menu-back-button:hover,
    .allmenu .static-menu .front-menu-back-button:focus,
    .allmenu .static-menu .front-menu-login:hover,
    .allmenu .static-menu .front-menu-login:focus {
      border-color: #ffd200;
      background: #ffd200;
      color: #285f10;
    }
  }

  .allmenu .static-menu>.user-menu-dropdown {
    margin: 0 6px;
    padding-top: 0;
    position: relative;
    vertical-align: middle;
  }

  .allmenu .static-menu #userMenuToggle {
    min-height: 42px;
    padding: 5px 12px;
    border-color: rgba(255, 255, 255, 0.35) !important;
    background: rgba(255, 255, 255, 0.12) !important;
    color: #fff !important;
  }

  .allmenu .static-menu #userMenuToggle:hover,
  .allmenu .static-menu #userMenuToggle:focus {
    border-color: rgba(255, 255, 255, 0.65) !important;
    background: rgba(255, 255, 255, 0.22) !important;
    color: #fff !important;
  }

  .allmenu .static-menu #userMenuToggle:focus-visible {
    outline: 2px solid #fff;
    outline-offset: 2px;
  }

  .allmenu .static-menu .user-menu-avatar {
    background: linear-gradient(135deg, #fff, #e7f6df) !important;
    color: #39841b !important;
    flex-shrink: 0;
  }

  .allmenu .static-menu .user-menu-avatar-large {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #fff;
    flex-shrink: 0;
  }

  .allmenu .static-menu #userDropdownMenu {
    top: calc(100% + 8px) !important;
    width: 300px;
    max-width: calc(100vw - 24px);
    text-align: left;
  }

  .allmenu .static-menu .user-menu-header {
    background: linear-gradient(135deg, #51aa1b, #347f12) !important;
  }

  .allmenu .static-menu .user-dropdown-item:hover,
  .allmenu .static-menu .user-dropdown-item:focus {
    background: #f2f8ee !important;
    color: #347f12 !important;
  }

  .allmenu .static-menu .user-dropdown-item i {
    color: #51aa1b !important;
  }

  .allmenu .static-menu .user-logout-btn:focus-visible {
    outline: 2px solid #dc2626;
    outline-offset: 2px;
  }

  @media (min-width: 768px) and (max-width: 991px) {
    .allmenu .static-menu {
      justify-content: flex-start;
      gap: 3px;
    }

    .allmenu .static-menu>li>.front-menu-link {
      padding: 0 7px;
      font-size: 13px;
    }
  }
</style>

<div class="allmenu">
  <div class="container">
    <div class="row">

      {{-- Left categories --}}
      <div class="col-md-3 col-sm-4">
        <div class="hidden-xs">
          <div id="wr-menu">
            <button class="btn-block text-left" type="button" data-target="#all-menu" data-toggle="collapse">
              <i class="fa fa-bars"></i>
              <span class="cate">Toutes les Categories</span>
            </button>
          </div>

          <div id="all-menu" class="collapse">
            <nav id="menu" class="navbar">
              <div class="navbar-header">
                <span id="category" class="visible-xs">Toutes les Categories</span>
                <button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                  <i class="fa fa-bars"></i>
                </button>
              </div>

              <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav">
                  @foreach($navCategories as $c)
                  <li class="moremenu ">
                    <a href="{{ route('category', ['slug' => $c->id]) }}">
                      <div class="menu-img pull-left">
                        <img src="{{ asset($repertCat.$c->photo) }}" alt="{{ $c->libelle }}">
                      </div>
                      {{ $c->libelle }} ({{ $c->nbprod }})
                    </a>
                  </li>
                  @endforeach
                </ul>
              </div>
            </nav>
          </div>
        </div>

        {{-- Mobile menu --}}
        <div class="hidden-sm hidden-md hidden-lg">
          <nav id="menu" class="navbar">
            <div class="navbar-header">
              <button type="button" class="btn btn-navbar" onclick="openNav()" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <i class="fa fa-bars"></i>
              </button>
            </div>

            <div id="mySidenav" class="sidenav">
              <div class="close-nav">
                <span class="categories">Category</span>
                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()"><i class="fa fa-close"></i></a>
              </div>

              <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                  @foreach($navCategories as $c)
                  <li><a href="{{ route('category', ['slug' => $c->id]) }}">{{ $c->libelle }}</a></li>
                  @endforeach
                </ul>
              </div>
            </div>
          </nav>
        </div>

        <script>
          function headermenu() {
            if (jQuery(window).width() < 768) {
              jQuery('ul.nav li.dropdown a.header-menu').attr("data-toggle", "dropdown");
            } else {
              jQuery('ul.nav li.dropdown a.header-menu').attr("data-toggle", "");
            }
          }
          $(document).ready(function() {
            headermenu();
          });
          jQuery(window).resize(function() {
            headermenu();
          });
          jQuery(window).scroll(function() {
            headermenu();
          });
        </script>
      </div>

      {{-- Right menu --}}
      <div class="col-md-9 col-sm-8" role="navigation" aria-label="Navigation principale">
        <ul class="list-inline static-menu hidden-xs">
          <li><a class="front-menu-link" href="{{ route('home') }}">Accueil</a></li>
          <li><a class="front-menu-link" href="{{ route('category') }}">Catalogue</a></li>
          <li><a class="front-menu-link" href="{{ route('contact') }}">Contact</a></li>
          <!-- <li><a href="{{ route('sitemap') }}">Site Map</a></li> -->
          <li><a class="front-menu-link" href="{{ route('brands') }}">Marques</a></li>
          <li><a class="front-menu-link" href="{{ route('suivicoli') }}">Suivi des colis</a></li>
          @if(Auth::check())
          @php
          $menusUser = session('menusUser');
          @endphp
          @if(is_array($menusUser) && in_array(11, $menusUser))
          <li class="front-menu-back">
            <button type="button" class="front-menu-back-button"
              onclick="afficher('back');"><i class="fa fa-arrow-left" aria-hidden="true"></i>
              &nbsp; Back</button>
          </li>
          @endif
          @include('back.includes.menuAuth')
          <!-- <li><a href="{{ route('logout') }}">Se déconnecter</a></li> -->
          @else
          <li><a class="front-menu-login" href="{{ route('login') }}">Se connecter</a></li>
          @endif
        </ul>
        @if(Auth::check())
        <script>
          (function() {
            var toggle = document.getElementById('userMenuToggle');
            var dropdown = document.getElementById('userDropdownMenu');

            if (!toggle || !dropdown) {
              return;
            }

            function closeMenu() {
              dropdown.style.display = 'none';
              toggle.setAttribute('aria-expanded', 'false');
              var chevron = toggle.querySelector('.fa-chevron-down');
              if (chevron) {
                chevron.style.transform = 'rotate(0deg)';
              }
            }

            toggle.addEventListener('click', function(event) {
              event.preventDefault();
              event.stopPropagation();

              var isOpen = dropdown.style.display === 'block';
              dropdown.style.display = isOpen ? 'none' : 'block';
              toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');

              var chevron = toggle.querySelector('.fa-chevron-down');
              if (chevron) {
                chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
              }
            });

            document.addEventListener('click', function(event) {
              if (!toggle.contains(event.target) && !dropdown.contains(event.target)) {
                closeMenu();
              }
            });

            document.addEventListener('keydown', function(event) {
              if (event.key === 'Escape' && dropdown.style.display === 'block') {
                closeMenu();
                toggle.focus();
              }
            });
          })();
        </script>
        @endif
      </div>

    </div>
  </div>
</div>

{{-- Preloader --}}
<div id="page-preloader" class="visible">
  <div class="preloader">
    <div id="loading-center-absolute">
      <div class="object" id="object_one"></div>
    </div>
  </div>
</div>

<div id="common-home" class="container-fluid">
  <div class="row">