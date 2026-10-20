@extends('layoutfront')

@php
  $title = ($currentCategory!=null)? $currentCategory->libelle: 'Toutes les catégories';
  $repertCat = "storage/images/Categories/";
  $repertProd = "storage/images/Articles/";
  $categoryUrl = $currentCategory
    ? route('category', ['slug' => $currentCategory->id])
    : route('category');
@endphp
@section('title', $title)


@section('content')
<div id="product-category" class="container">
  <ul class="breadcrumb">
    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i></a></li>
    <li><a href="{{ route('category') }}">Catégories</a></li>
    @if($currentCategory!=null)
      <li><a href="{{ route('category', ['slug' => $currentCategory->id]) }}">{{ $currentCategory->libelle }}</a></li>
    @endif
  </ul>

  <div class="row">
    <aside id="column-left" class="col-sm-3">
      <div class="list-group">
        <a class="list-group-item {{ $currentCategory ==null ? 'active' : '' }}" href="{{ route('category') }}">Toutes les catégories</a>
        @foreach($categories as $c)
          <a class="list-group-item {{ ($currentCategory!=null && (string) $currentCategory->id === (string) $c->id) ? 'active' : '' }}" href="{{ route('category', ['slug' => $c->id]) }}">
            {{ $c->libelle }} <span class="pull-right">({{ $c->nbprod }})</span>
          </a>
        @endforeach
      </div>

      <div class="panel panel-default">
        <div class="panel-heading"><strong>Filtrer les produits</strong></div>
        <div class="panel-body">
          <form method="GET" action="{{ $categoryUrl }}" id="category-filters">
            <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
            <div class="form-group">
              <label for="filterBrand">Marque</label>
              <select class="form-control" id="filterBrand" name="brand">
                <option value="">Toutes les marques</option>
                @foreach($brands as $b)
                  <option value="{{ $b->id }}" {{ $filters['brand'] === (string) $b->id ? 'selected' : '' }}>{{ $b->libelle }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>Prix (€)</label>
              <div class="row">
                <div class="col-xs-6"><input class="form-control" type="number" name="min_price" placeholder="Min" value="{{ $filters['min_price'] }}" min="0" step="0.01" aria-label="Prix minimum"></div>
                <div class="col-xs-6"><input class="form-control" type="number" name="max_price" placeholder="Max" value="{{ $filters['max_price'] }}" min="0" step="0.01" aria-label="Prix maximum"></div>
              </div>
            </div>
            @if($errors->any())
              <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
            @endif
            <button class="btn btn-primary btn-block" type="submit">Appliquer les filtres</button>
            <a class="btn btn-default btn-block" href="{{ $categoryUrl }}">Réinitialiser</a>
          </form>
        </div>
      </div>
    </aside>

    <div id="content" class="col-sm-9">
      <h2>{{ $title }}</h2>

      @if($currentCategory!=null)
        <div class="row" style="margin-bottom: 15px;">
          <div class="col-sm-12">
            <div class="well" style="margin-bottom: 0;">
              <div class="row">
                <div class="col-xs-12 col-sm-4">
                  <img class="img-responsive" src="{{ asset($repertCat.$currentCategory->photo) }}" alt="{{ $currentCategory->libelle }}">
                </div>
                <div class="col-xs-12 col-sm-8">
                  <p style="margin-top: 10px;">
                    Retrouvez notre sélection <strong>{{ strtolower($currentCategory->libelle) }}</strong> et filtrez les produits selon vos besoins.
                  </p>
                  <ul class="list-inline" style="margin:0;">
                    <li><i class="fa fa-check"></i> Livraison rapide</li>
                    <li><i class="fa fa-check"></i> Paiement sécurisé</li>
                    <li><i class="fa fa-check"></i> Service client</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif

      <div class="row">
        <div class="col-sm-6">
          <div class="btn-group">
            <button type="button" id="btnGrid" class="btn btn-default" data-toggle="tooltip" title="Grille"><i class="fa fa-th"></i></button>
            <button type="button" id="btnList" class="btn btn-default" data-toggle="tooltip" title="Liste"><i class="fa fa-th-list"></i></button>
          </div>
        </div>
        <div class="col-sm-6 text-right">
          <div class="form-inline">
            <label class="control-label">Trier par</label>
            <form method="GET" action="{{ $categoryUrl }}" class="form-inline" id="category-sort">
              <input type="hidden" name="brand" value="{{ $filters['brand'] }}">
              <input type="hidden" name="min_price" value="{{ $filters['min_price'] }}">
              <input type="hidden" name="max_price" value="{{ $filters['max_price'] }}">
              <label class="control-label" for="sortBy">Trier par</label>
              <select class="form-control" id="sortBy" name="sort">
                <option value="popular" {{ $filters['sort'] === 'popular' ? 'selected' : '' }}>Nouveautés</option>
                <option value="name" {{ $filters['sort'] === 'name' ? 'selected' : '' }}>Nom</option>
                <option value="priceAsc" {{ $filters['sort'] === 'priceAsc' ? 'selected' : '' }}>Prix (croissant)</option>
                <option value="priceDesc" {{ $filters['sort'] === 'priceDesc' ? 'selected' : '' }}>Prix (décroissant)</option>
              </select>
            </form>
          </div>
        </div>
      </div>
      <hr>

      <div class="row" id="productGrid">
        @forelse($products as $p)
          <div class="product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="product-thumb transition">
              <div class="image">
                <a href="{{ route('product', ['slug' => $p->id]) }}">
                  @if($p->photo1)
                    <img src="{{ asset($repertProd.$p->photo1) }}" alt="{{ $p->libelle }}" class="img-responsive center-block" />
                  @else
                    <span class="category-product-placeholder" aria-hidden="true"><i class="fa fa-shopping-basket"></i></span>
                  @endif
                </a>
                @if($p->old_price > 0)
                  @php
                    $save = (int) round((1 - ($p->prix / $p->old_price)) * 100);
                  @endphp
                  <p class="sale-tag hot">Save {{ $save }}%</p>
                @endif
              </div>
              <div class="caption text-center">
                <h4><a href="{{ route('product', ['slug' => $p->id]) }}">{{ $p->libelle}}</a></h4>
                <p class="price">
                  @if($p->old_price > 0)
                    <span class="price-new">{{ number_format($p->prix, 2, ',', ' ') }} €</span>
                    <span class="price-old">{{ number_format($p->old_price, 2, ',', ' ') }} €</span>
                  @else
                    {{ number_format($p->prix, 2, ',', ' ') }} €
                  @endif
                </p>
                <div class="button-group">
                  <button type="button" data-toggle="tooltip" title="Ajouter à la liste de souhaits" onclick="lemastore.toast('Ajouté à la liste de souhaits (maquette).')">
                    <svg width="16px" height="16px"><use xlink:href="#addwish"></use></svg>
                  </button>
                  <button type="button" data-toggle="tooltip" title="Comparer" onclick="lemastore.toast('Ajouté au comparateur (maquette).')">
                    <svg width="16px" height="16px"><use xlink:href="#addcompare"></use></svg>
                  </button>
                  <div class="bquickv" data-toggle="tooltip" title="Quick View" onclick="lemastore.toast('Quick view (maquette).')"></div>
                </div>
                <div style="margin-top: 10px;">
                  <a class="btn btn-primary" href="{{ route('product', ['slug' => $p->id]) }}">Voir le produit</a>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-xs-12">
            <div class="alert alert-info">Aucun produit ne correspond à ces critères.</div>
            <a href="{{ $categoryUrl }}" class="btn btn-default">Effacer les filtres</a>
          </div>
        @endforelse
      </div>

      @if($products->total() > 0)
        <div class="row category-pagination">
          <div class="col-sm-6">
            <p class="help-block">
              Affichage de {{ $products->firstItem() }} à {{ $products->lastItem() }} sur {{ $products->total() }} produit(s)
            </p>
          </div>
          <div class="col-sm-6 text-right">
            {{ $products->onEachSide(1)->links('pagination::bootstrap-4') }}
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<script>
  (function(){
    var gridBtn = document.getElementById('btnGrid');
    var listBtn = document.getElementById('btnList');
    var grid = document.getElementById('productGrid');

    function setList(isList){
      if(!grid) return;
      var cards = grid.querySelectorAll('.product-layout');
      cards.forEach(function(card){
        card.classList.toggle('product-list', isList);
        card.classList.toggle('product-grid', !isList);
        if(isList){
          card.classList.remove('col-lg-4','col-md-4','col-sm-6');
          card.classList.add('col-xs-12');
        } else {
          card.classList.add('col-lg-4','col-md-4','col-sm-6');
          card.classList.remove('col-xs-12');
        }
      });
    }

    if(gridBtn) gridBtn.addEventListener('click', function(){ setList(false); });
    if(listBtn) listBtn.addEventListener('click', function(){ setList(true); });

    var sortForm = document.getElementById('category-sort');
    var sortSelect = document.getElementById('sortBy');
    if(sortForm && sortSelect) {
      sortSelect.addEventListener('change', function(){ sortForm.submit(); });
    }
  })();
</script>
@endsection
