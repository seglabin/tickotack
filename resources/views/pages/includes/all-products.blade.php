<section class="container home-products" aria-labelledby="home-products-title">
    <div class="home-section-heading">
        <div>
            <p class="home-eyebrow">Nos nouveautés</p>
            <h2 id="home-products-title">Une sélection pour vous</h2>
        </div>
        <a href="{{ route('category') }}" class="home-text-link">Voir tout le catalogue <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
    </div>

    @if($homeCategories->isNotEmpty())
        <ul class="nav nav-tabs home-product-tabs" role="tablist">
            @foreach($homeCategories as $index => $category)
                <li class="{{ $index === 0 ? 'active' : '' }}" role="presentation">
                    <a href="#home-category-{{ $category->id }}" aria-controls="home-category-{{ $category->id }}" role="tab" data-toggle="tab">
                        {{ $category->libelle }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content home-product-tab-content">
            @foreach($homeCategories as $index => $category)
                <div class="tab-pane {{ $index === 0 ? 'active' : '' }}" id="home-category-{{ $category->id }}" role="tabpanel">
                    <div class="row home-product-grid">
                        @foreach($homeProducts->where('idcategorie', $category->id)->take(8) as $product)
                            <div class="col-xs-6 col-sm-4 col-md-3">
                                <article class="product-thumb home-product-card">
                                    <div class="image">
                                        <a href="{{ route('product', ['slug' => $product->id]) }}" aria-label="Voir {{ $product->libelle }}">
                                            @if($product->photo1)
                                                <img src="{{ asset('storage/images/Articles/' . $product->photo1) }}" alt="{{ $product->libelle }}">
                                            @else
                                                <span class="home-product-placeholder"><i class="fa fa-shopping-basket" aria-hidden="true"></i></span>
                                            @endif
                                        </a>
                                        @if($product->old_price > $product->prix)
                                            <span class="home-sale-badge">Promo</span>
                                        @endif
                                    </div>
                                    <div class="caption">
                                        @if($product->libmarque)
                                            <p class="home-product-brand">{{ $product->libmarque }}</p>
                                        @endif
                                        <h3><a href="{{ route('product', ['slug' => $product->id]) }}">{{ $product->libelle }}</a></h3>
                                        <p class="home-product-price">
                                            {{ number_format($product->prix, 0, ',', ' ') }} €
                                            @if($product->old_price > $product->prix)
                                                <del>{{ number_format($product->old_price, 0, ',', ' ') }} €</del>
                                            @endif
                                        </p>
                                        <div class="home-product-actions">
                                            <button type="button" class="btn btn-primary" onclick="ajoutpanier('{{ $product->id }}', 1)">
                                                <i class="fa fa-shopping-basket" aria-hidden="true"></i> Ajouter au panier
                                            </button>
                                            <a href="{{ route('product', ['slug' => $product->id]) }}" class="home-product-details">Détails</a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                    <p class="home-category-link">
                        <a href="{{ route('category', ['slug' => $category->id]) }}">Voir tous les produits {{ strtolower($category->libelle) }} <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                    </p>
                </div>
            @endforeach
        </div>
    @else
        <div class="home-empty-catalog">
            <i class="fa fa-shopping-basket" aria-hidden="true"></i>
            <p>Les produits arrivent bientôt.</p>
            <a href="{{ route('category') }}" class="btn btn-primary">Parcourir le catalogue</a>
        </div>
    @endif
</section>

<style>
    .storefront-home .home-products {
        padding-top: 44px;
        padding-bottom: 28px;
    }

    .storefront-home .home-product-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 22px;
        border-bottom-color: #e7ede8;
    }

    .storefront-home .home-product-tabs > li > a {
        border: 0;
        border-radius: 20px;
        color: #536359;
        font-weight: 600;
    }

    .storefront-home .home-product-tabs > li.active > a,
    .storefront-home .home-product-tabs > li.active > a:hover,
    .storefront-home .home-product-tabs > li.active > a:focus {
        border: 0;
        background: #edf6ef;
        color: #185333;
    }

    #content.storefront-home .home-product-grid {
        display: flex;
        flex-wrap: wrap;
    }

    .storefront-home .home-product-grid > [class*="col-"] {
        display: flex;
        margin-bottom: 18px;
    }

    .storefront-home .home-product-card {
        display: flex;
        width: 100%;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e7ede8;
        border-radius: 7px;
        background: #fff;
        box-shadow: 0 4px 18px rgba(26, 63, 39, .04);
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .storefront-home .home-product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(26, 63, 39, .1);
    }

    #content.storefront-home .home-product-card .image {
        height: 210px;
        position: relative;
        border-bottom: 1px solid #f0f3f0;
    }

    #content.storefront-home .home-product-card .image > a {
        display: flex;
        width: 100%;
        height: 100%;
        align-items: center;
        justify-content: center;
    }

    .storefront-home .home-sale-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 4px 9px;
        border-radius: 12px;
        background: #eaf5ec;
        color: #287a48;
        font-size: 11px;
        font-weight: 700;
    }

    .storefront-home .home-product-card .caption {
        display: flex;
        flex: 1;
        flex-direction: column;
        align-items: flex-start;
        padding: 14px;
    }

    .storefront-home .home-product-brand {
        margin: 0 0 5px;
        color: #718076;
        font-size: 11px;
        text-transform: uppercase;
    }

    .storefront-home .home-product-card h3 {
        min-height: 40px;
        margin: 0 0 8px;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.45;
    }

    .storefront-home .home-product-card h3 a {
        color: #25332b;
    }

    .storefront-home .home-product-price {
        margin: 0 0 12px;
        color: #287a48;
        font-size: 17px;
        font-weight: 700;
    }

    .storefront-home .home-product-price del {
        margin-left: 6px;
        color: #8b968e;
        font-size: 12px;
        font-weight: 400;
    }

    .storefront-home .home-product-actions {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: auto;
    }

    .storefront-home .home-product-actions .btn {
        padding: 7px 9px;
        font-size: 12px;
    }

    .storefront-home .home-product-details,
    .storefront-home .home-category-link a {
        color: #287a48;
        font-size: 12px;
        font-weight: 700;
    }

    .storefront-home .home-category-link {
        margin: 4px 0 0;
        text-align: right;
    }

    .storefront-home .home-empty-catalog {
        padding: 34px 20px;
        border: 1px dashed #d6e2d8;
        border-radius: 7px;
        background: #f8faf8;
        text-align: center;
    }

    .storefront-home .home-empty-catalog > i {
        color: #8ba494;
        font-size: 30px;
    }

    .storefront-home .home-empty-catalog p {
        margin: 10px 0 16px;
        color: #6d7b72;
    }

    @media (max-width: 480px) {
        #content.storefront-home .home-product-card .image {
            height: 160px;
        }

        .storefront-home .home-product-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .storefront-home .home-product-actions .btn {
            width: 100%;
        }
    }
</style>