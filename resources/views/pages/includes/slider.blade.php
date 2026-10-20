<div class="container">
    <div class="row">
        <div class="col-md-8 col-sm-12 home-hero-column">
            <div class="home-hero-carousel owl-carousel">
                @forelse ($sliders as $s)
                    <article class="home-hero-slide">
                        <div class="home-hero-copy">
                            <p class="home-hero-eyebrow">La sélection du moment</p>
                            <h1>{{ $s->libelle }}</h1>
                            <p class="home-hero-description">
                                {{ \Illuminate\Support\Str::limit(strip_tags($s->description ?? 'Découvrez notre sélection et trouvez facilement ce qu’il vous faut.'), 125) }}
                            </p>
                            <div class="home-hero-actions">
                                <a href="{{ route('product', ['slug' => $s->id]) }}" class="home-hero-button">Découvrir le produit</a>
                                <a href="{{ route('category') }}" class="home-hero-catalog-link">Voir le catalogue <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                            </div>
                            <p class="home-hero-price">{{ number_format($s->prix, 0, ',', ' ') }} €</p>
                        </div>
                        <a href="{{ route('product', ['slug' => $s->id]) }}" class="home-hero-image" aria-label="Voir {{ $s->libelle }}">
                            @if($s->photo1)
                                <img src="{{ asset('storage/images/Articles/' . $s->photo1) }}" alt="{{ $s->libelle }}">
                            @else
                                <span class="home-product-placeholder"><i class="fa fa-shopping-basket" aria-hidden="true"></i></span>
                            @endif
                        </a>
                    </article>
                @empty
                    <article class="home-hero-slide home-hero-slide-fallback">
                        <div class="home-hero-copy">
                            <p class="home-hero-eyebrow">Bienvenue chez Global Vision</p>
                            <h1>Tout ce qu’il vous faut, simplement.</h1>
                            <p class="home-hero-description">Explorez notre catalogue, découvrez les nouveautés et composez votre panier en quelques clics.</p>
                            <div class="home-hero-actions">
                                <a href="{{ route('category') }}" class="home-hero-button">Découvrir le catalogue</a>
                            </div>
                        </div>
                        <div class="home-hero-art" aria-hidden="true"><i class="fa fa-shopping-basket"></i></div>
                    </article>
                @endforelse
            </div>
        </div>
        <aside class="col-md-4 home-hero-promos" aria-label="Accès rapides aux catégories">
            @forelse($categtop->take(2) as $category)
                <a class="home-category-promo" href="{{ route('category', ['slug' => $category->id]) }}">
                    @if($category->photo)
                        <img src="{{ asset('storage/images/Categories/' . $category->photo) }}" alt="">
                    @else
                        <span class="home-promo-placeholder"><i class="fa fa-leaf" aria-hidden="true"></i></span>
                    @endif
                    <span class="home-category-promo-copy">
                        <span class="home-hero-eyebrow">À explorer</span>
                        <strong>{{ $category->libelle }}</strong>
                        <span>{{ $category->nbprod }} produit(s) <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
                    </span>
                </a>
            @empty
                <a class="home-category-promo home-category-promo-simple" href="{{ route('category') }}">
                    <span class="home-promo-placeholder"><i class="fa fa-th-large" aria-hidden="true"></i></span>
                    <span class="home-category-promo-copy">
                        <span class="home-hero-eyebrow">Tout le choix</span>
                        <strong>Parcourir le catalogue</strong>
                        <span>Voir toutes les catégories <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
                    </span>
                </a>
                <a class="home-category-promo home-category-promo-simple" href="{{ route('contact') }}">
                    <span class="home-promo-placeholder"><i class="fa fa-commenting-o" aria-hidden="true"></i></span>
                    <span class="home-category-promo-copy">
                        <span class="home-hero-eyebrow">Besoin d’aide ?</span>
                        <strong>Nous contacter</strong>
                        <span>Poser une question <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
                    </span>
                </a>
            @endforelse
        </aside>
    </div>
</div>

@push('scripts')
<script>
    $(function () {
        var carousel = $('.home-hero-carousel');
        if (carousel.children().length > 1) {
            carousel.owlCarousel({
                items: 1,
                itemsCustom: [[0, 1]],
                autoPlay: 5000,
                stopOnHover: true,
                navigation: true,
                pagination: true,
                navigationText: [
                    '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                    '<i class="fa fa-angle-right" aria-hidden="true"></i>'
                ]
            });
        }
    });
</script>
@endpush