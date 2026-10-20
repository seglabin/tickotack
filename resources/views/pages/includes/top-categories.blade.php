<div class="container">
    <div class="cat-img">
        <div class="pro-bg pro-nepr">
            <div class="home-section-heading">
                <div>
                    <p class="home-eyebrow">Faites votre choix</p>
                    <h2>Explorer les catégories</h2>
                </div>
                <a href="{{ route('category') }}" class="home-text-link">Tout le catalogue <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
            </div>

            <div class="row cat-mr">
                <div class="beyque-p">
                    <div id="featured_category">
                        <div class="category_module p-0">
                            <div class="beyqu-center-column-btn">
                                @forelse ($categtop as $c)
                                    <div class="block-cat-wr col-xs-6 col-sm-4 col-md-3 pl-5 pr-5">
                                        <a href="{{ route('category', ['slug' => $c->id]) }}">
                                            @if($c->photo)
                                                <img src="{{ asset('storage/images/Categories/' . $c->photo) }}"
                                                    class="img-responsive center-block" alt="{{ $c->libelle }}">
                                            @else
                                                <span class="home-category-placeholder"><i class="fa fa-leaf" aria-hidden="true"></i></span>
                                            @endif
                                        </a>
                                        <h4 class="text-center cat-name-height">
                                            <a href="{{ route('category', ['slug' => $c->id]) }}">
                                                {{ $c->libelle }} ({{ $c->nbprod }})
                                            </a>
                                        </h4>
                                    </div>
                                @empty
                                    <div class="col-xs-12">
                                        <p class="text-muted">Les catégories seront bientôt disponibles.</p>
                                        <a href="{{ route('category') }}" class="btn btn-default">Parcourir le catalogue</a>
                                    </div>
                                @endforelse
                            </div> {{-- /.beyqu-center-column-btn --}}
                        </div> {{-- /.category_module --}}
                    </div> {{-- /#featured_category --}}
                </div>
            </div>

        </div>
    </div>
</div>