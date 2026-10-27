@extends('layoutfront')
@section('title', 'Accueil')
@section('content')

@include('pages.includes.suivi-colis-modal')

<div id="content" class="col-sm-12 storefront-home">

  <!-- Mise en avant -->
  @include('pages.includes.slider')

  <section class="home-benefits" aria-label="Nos engagements">
    <div class="container">
      <div class="row">
        <div class="col-sm-4">
          <div class="home-benefit">
            <i class="fa fa-shopping-basket" aria-hidden="true"></i>
            <div><strong>Un catalogue pratique</strong><span>Retrouvez rapidement vos essentiels.</span></div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="home-benefit">
            <i class="fa fa-credit-card" aria-hidden="true"></i>
            <div><strong>Un achat en toute simplicité</strong><span>Parcourez les produits et gérez votre panier.</span></div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="home-benefit">
            <i class="fa fa-truck" aria-hidden="true"></i>
            <div><strong>Un suivi accessible</strong><span>Consultez à tout moment le statut de votre colis.</span></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Catégories -->
  @include('pages.includes.top-categories')

  <!-- Sélection de produits -->
  @include('pages.includes.all-products')

  @if($homeBrands->isNotEmpty())
    <section class="home-brands container" aria-labelledby="home-brands-title">
      <div class="home-section-heading">
        <div>
          <p class="home-eyebrow">Nos partenaires</p>
          <h2 id="home-brands-title">Quelques marques présentes au catalogue</h2>
        </div>
        <a href="{{ route('category') }}" class="home-text-link">Voir le catalogue <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
      </div>
      <div class="home-brand-list">
        @foreach($homeBrands as $brand)
          <span class="home-brand-pill">{{ $brand->libelle }}</span>
        @endforeach
      </div>
    </section>
  @endif

  <section class="home-tracking">
    <div class="container">
      <div class="home-tracking-content">
        <div>
          <p class="home-eyebrow">Besoin d’une information ?</p>
          <h2>Suivez votre colis en quelques secondes</h2>
          <p>Munissez-vous de votre numéro de colis pour consulter son statut et son historique.</p>
        </div>
        <button type="button" class="btn home-tracking-button" data-toggle="modal" data-target="#modalSuiviColis">
          Suivre un colis <i class="fa fa-arrow-right" aria-hidden="true"></i>
        </button>
      </div>
    </div>
  </section>

</div>

</div>
</div>
@endsection

@push('head')
<style>
  .storefront-home {
    --home-green: #287a48;
    --home-green-dark: #185333;
    --home-ink: #25332b;
    --home-muted: #6d7b72;
    --home-line: #e7ede8;
    color: var(--home-ink);
  }

  .storefront-home .home-section-heading {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 22px;
  }

  .storefront-home .home-hero-column {
    padding-left: 0;
  }

  .storefront-home .home-hero-carousel {
    margin-bottom: 0;
    overflow: hidden;
    border-radius: 9px;
    background: #f2f6f1;
  }

  .storefront-home .home-hero-slide {
    display: flex;
    min-height: 360px;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 38px 34px;
    background: linear-gradient(115deg, #f2f6f1 0%, #f8faf7 65%, #e8f0e8 100%);
  }

  .storefront-home .home-hero-copy {
    position: relative;
    z-index: 1;
    width: 56%;
  }

  .storefront-home .home-hero-eyebrow {
    display: block;
    margin-bottom: 10px;
    color: var(--home-green);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
  }

  .storefront-home .home-hero-copy h1 {
    margin: 0 0 12px;
    color: var(--home-ink);
    font-size: clamp(26px, 3vw, 42px);
    font-weight: 700;
    line-height: 1.12;
  }

  .storefront-home .home-hero-description {
    max-width: 410px;
    margin: 0 0 22px;
    color: var(--home-muted);
    line-height: 1.7;
  }

  .storefront-home .home-hero-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
  }

  .storefront-home .home-hero-button {
    display: inline-block;
    padding: 12px 18px;
    border-radius: 4px;
    background: var(--home-green);
    color: #fff;
    font-weight: 700;
  }

  .storefront-home .home-hero-button:hover,
  .storefront-home .home-hero-button:focus {
    background: var(--home-green-dark);
    color: #fff;
    text-decoration: none;
  }

  .storefront-home .home-hero-catalog-link {
    color: var(--home-green-dark);
    font-size: 13px;
    font-weight: 700;
  }

  .storefront-home .home-hero-price {
    margin: 18px 0 0;
    color: var(--home-green);
    font-size: 20px;
    font-weight: 700;
  }

  .storefront-home .home-hero-image {
    display: flex;
    width: 42%;
    height: 275px;
    align-items: center;
    justify-content: center;
  }

  .storefront-home .home-hero-image img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    mix-blend-mode: multiply;
  }

  .storefront-home .home-hero-art {
    display: flex;
    width: 38%;
    height: 240px;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #e2eee3;
    color: var(--home-green);
    font-size: 96px;
  }

  .storefront-home .home-hero-promos {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding-right: 0;
  }

  .storefront-home .home-category-promo {
    position: relative;
    display: flex;
    min-height: 173px;
    flex: 1;
    align-items: center;
    overflow: hidden;
    padding: 20px;
    border-radius: 8px;
    background: #eef4ed;
    color: var(--home-ink);
  }

  .storefront-home .home-category-promo:hover,
  .storefront-home .home-category-promo:focus {
    color: var(--home-ink);
    text-decoration: none;
  }

  .storefront-home .home-category-promo > img {
    position: absolute;
    top: 8%;
    right: 2%;
    width: 45%;
    height: 84%;
    object-fit: contain;
    mix-blend-mode: multiply;
    transition: transform .2s ease;
  }

  .storefront-home .home-category-promo:hover > img {
    transform: scale(1.04);
  }

  .storefront-home .home-category-promo-copy {
    position: relative;
    z-index: 1;
    display: flex;
    max-width: 62%;
    flex-direction: column;
    gap: 7px;
  }

  .storefront-home .home-category-promo-copy strong {
    color: var(--home-ink);
    font-size: 19px;
    line-height: 1.25;
  }

  .storefront-home .home-category-promo-copy > span:last-child {
    color: var(--home-green);
    font-size: 12px;
    font-weight: 700;
  }

  .storefront-home .home-promo-placeholder,
  .storefront-home .home-category-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e4eee5;
    color: #82a38a;
  }

  .storefront-home .home-promo-placeholder {
    position: absolute;
    top: 8%;
    right: 2%;
    width: 45%;
    height: 84%;
    font-size: 48px;
  }

  .storefront-home .home-category-placeholder {
    width: 100%;
    height: 150px;
    font-size: 32px;
  }

  .storefront-home .cat-img {
    padding-top: 38px;
  }

  #content.storefront-home .cat-img .block-cat-wr img {
    width: 100%;
    height: 150px;
    object-fit: contain;
  }

  .storefront-home .cat-img .block-cat-wr h4 {
    min-height: 38px;
  }

  .storefront-home .home-section-heading h2,
  .storefront-home .home-tracking h2 {
    margin: 5px 0 0;
    color: var(--home-ink);
    font-size: 28px;
    font-weight: 700;
  }

  .storefront-home .home-eyebrow {
    margin: 0;
    color: var(--home-green);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
  }

  .storefront-home .home-text-link {
    color: var(--home-green);
    font-weight: 700;
    white-space: nowrap;
  }

  .storefront-home .home-benefits {
    padding: 22px 0;
    border-bottom: 1px solid var(--home-line);
  }

  .storefront-home .home-benefit {
    display: flex;
    align-items: center;
    gap: 14px;
    min-height: 58px;
  }

  .storefront-home .home-benefit > i {
    display: inline-flex;
    flex: 0 0 44px;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #edf6ef;
    color: var(--home-green);
    font-size: 18px;
  }

  .storefront-home .home-benefit strong,
  .storefront-home .home-benefit span {
    display: block;
  }

  .storefront-home .home-benefit strong {
    margin-bottom: 4px;
    font-size: 14px;
  }

  .storefront-home .home-benefit span {
    color: var(--home-muted);
    font-size: 12px;
  }

  .storefront-home .home-brands {
    padding-top: 42px;
    padding-bottom: 42px;
  }

  .storefront-home .home-brand-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .storefront-home .home-brand-pill {
    padding: 10px 18px;
    border: 1px solid var(--home-line);
    border-radius: 22px;
    background: #fff;
    color: var(--home-ink);
    font-weight: 600;
    transition: border-color .2s ease, color .2s ease, background .2s ease;
  }

  .storefront-home .home-brand-pill:hover,
  .storefront-home .home-brand-pill:focus {
    border-color: var(--home-green);
    background: #f3f8f4;
    color: var(--home-green-dark);
    text-decoration: none;
  }

  .storefront-home .home-tracking {
    margin-top: 10px;
    padding: 38px 0;
    background: #f3f7f3;
  }

  .storefront-home .home-tracking-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
  }

  .storefront-home .home-tracking-content > div {
    max-width: 650px;
  }

  .storefront-home .home-tracking-content p:last-child {
    margin: 10px 0 0;
    color: var(--home-muted);
  }

  .storefront-home .home-tracking-button {
    padding: 12px 22px;
    border: 0;
    border-radius: 4px;
    background: var(--home-green);
    color: #fff;
    font-weight: 700;
  }

  .storefront-home .home-tracking-button:hover,
  .storefront-home .home-tracking-button:focus {
    background: var(--home-green-dark);
    color: #fff;
  }

  #content.storefront-home .product-thumb .image {
    display: flex;
    height: 210px;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: #fff;
  }

  #content.storefront-home .product-thumb .image img {
    width: auto;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }

  .storefront-home .home-product-placeholder {
    display: flex;
    width: 100%;
    height: 100%;
    align-items: center;
    justify-content: center;
    background: #f4f7f4;
    color: #9aad9e;
    font-size: 36px;
  }

  @media (max-width: 767px) {
    .storefront-home .home-hero-column,
    .storefront-home .home-hero-promos {
      padding-right: 0;
      padding-left: 0;
    }

    .storefront-home .home-hero-slide {
      min-height: 0;
      align-items: flex-start;
      flex-direction: column;
      gap: 12px;
      padding: 22px 18px;
    }

    .storefront-home .home-hero-copy,
    .storefront-home .home-hero-image {
      width: 100%;
    }

    .storefront-home .home-hero-copy h1 {
      font-size: clamp(26px, 7vw, 36px);
    }

    .storefront-home .home-hero-description {
      margin-bottom: 16px;
    }

    .storefront-home .home-hero-image {
      height: 170px;
    }

    .storefront-home .home-hero-art {
      width: 100%;
      height: 140px;
      font-size: 56px;
    }

    .storefront-home .home-category-promo {
      min-height: 135px;
    }

    .storefront-home .home-section-heading {
      align-items: flex-start;
      gap: 10px;
    }

    .storefront-home .home-section-heading h2,
    .storefront-home .home-tracking h2 {
      font-size: 23px;
    }

    .storefront-home .home-benefit {
      padding: 8px 0;
    }

    .storefront-home .home-product-tabs {
      flex-wrap: nowrap;
      overflow-x: auto;
      overflow-y: hidden;
      padding: 2px 0 8px;
      scrollbar-width: thin;
      -webkit-overflow-scrolling: touch;
    }

    .storefront-home .home-product-tabs > li {
      flex: 0 0 auto;
    }

    .storefront-home .home-product-tabs > li > a {
      white-space: nowrap;
    }

    .storefront-home .home-product-card .caption {
      padding: 10px;
    }

    .storefront-home .home-product-card h3 {
      min-height: 0;
      overflow-wrap: anywhere;
    }

    .storefront-home .home-tracking-content {
      align-items: flex-start;
      flex-direction: column;
    }

    .storefront-home .home-tracking-button {
      width: 100%;
      min-height: 46px;
    }

    #content.storefront-home .product-thumb .image {
      height: 145px;
    }
  }

  @media (max-width: 480px) {
    .storefront-home .home-hero-image {
      height: 125px;
    }

    .storefront-home .home-hero-art {
      height: 105px;
      font-size: 48px;
    }

    .storefront-home .home-section-heading {
      flex-direction: column;
    }

    .storefront-home .home-hero-actions {
      align-items: flex-start;
      flex-direction: column;
      gap: 10px;
    }

    .storefront-home .home-hero-button {
      width: 100%;
      text-align: center;
    }

    .storefront-home .home-benefit {
      align-items: flex-start;
    }

    #content.storefront-home .product-thumb .image {
      height: 125px;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .storefront-home *,
    .storefront-home *::before,
    .storefront-home *::after {
      scroll-behavior: auto !important;
      transition-duration: .01ms !important;
    }
  }
</style>
@endpush