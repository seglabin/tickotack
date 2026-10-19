@extends('layoutfront')

@php
  $title = ($currentCategory!=null)? $currentCategory->libelle: 'Toutes les catégories';
  $repertCat = "storage/images/Categories/";
  $repertProd = "storage/images/Articles/";
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
    <aside id="column-left" class="col-sm-3 hidden-xs">
      <div class="list-group">
        <a class="list-group-item {{ $currentCategory ==null ? 'active' : '' }}" href="{{ route('category') }}">Toutes les catégories</a>
        @foreach($categories as $c)
          <a class="list-group-item {{ ($currentCategory!=null && $currentCategory->id===$c->id) ? 'active' : '' }}" href="{{ route('category', ['slug' => $c->id]) }}">
            {{ $c->libelle }} <span class="pull-right">({{ $c->nbprod }})</span>
          </a>
        @endforeach
      </div>

      <div class="panel panel-default">
        <div class="panel-heading"><strong>Filtrer</strong></div>
        <div class="panel-body">
          <div class="form-group">
            <label>Marque</label>
            <select class="form-control" id="filterBrand">
              <option value="">Toutes</option>
              @foreach($brands as $b)
                <option value="{{ $b->id }}">{{ $b->libelle }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Prix</label>
            <div class="row">
              <div class="col-xs-6"><input class="form-control" type="number" placeholder="Min" id="filterMin" min="0"></div>
              <div class="col-xs-6"><input class="form-control" type="number" placeholder="Max" id="filterMax" min="0"></div>
            </div>
          </div>
          <button class="btn btn-primary btn-block" type="button" id="filterApply">Appliquer</button>
          <button class="btn btn-default btn-block" type="button" id="filterReset">Réinitialiser</button>
          <p class="help-block" style="margin-top:10px;">Filtre simulé (sans base de données).</p>
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
                    Retrouvez notre sélection <strong>{{ strtolower($currentCategory->libelle) }}</strong> (maquette). Vous pourrez brancher la base de données plus tard.
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
            <select class="form-control" id="sortBy">
              <option value="popular">Populaire</option>
              <option value="name">Nom</option>
              <option value="priceAsc">Prix (croissant)</option>
              <option value="priceDesc">Prix (décroissant)</option>
            </select>
          </div>
        </div>
      </div>
      <hr>

      <div class="row" id="productGrid">
        @forelse($products as $p)
          <div class="product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12"
               data-brand="{{ $p->libmarque }}"
               data-price="{{ $p->prix }}">
            <div class="product-thumb transition">
              <div class="image">
                <a href="{{ route('product', ['slug' => $p->id]) }}">
                  <img src="{{ asset($repertProd.$p->photo1) }}" alt="{{ $p->libelle }}" class="img-responsive center-block" />
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
            <div class="alert alert-info">Aucun produit (maquette).</div>
          </div>
        @endforelse
      </div>

      <div class="row">
        <div class="col-sm-6"><p class="help-block">Affichage simulé, sans pagination réelle.</p></div>
        <div class="col-sm-6 text-right">
          <ul class="pagination">
            <li class="active"><span>1</span></li>
            <li><a href="#" onclick="return false;">2</a></li>
            <li><a href="#" onclick="return false;">3</a></li>
            <li><a href="#" onclick="return false;">&raquo;</a></li>
          </ul>
        </div>
      </div>
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

    // Filtre mock
    function applyFilter(){
      var brand = (document.getElementById('filterBrand')||{}).value || '';
      var min = parseFloat((document.getElementById('filterMin')||{}).value || '');
      var max = parseFloat((document.getElementById('filterMax')||{}).value || '');
      var cards = grid.querySelectorAll('.product-layout');
      var shown = 0;
      cards.forEach(function(card){
        var b = card.getAttribute('data-brand');
        var price = parseFloat(card.getAttribute('data-price') || '0');
        var ok = true;
        if(brand && b !== brand) ok = false;
        if(!isNaN(min) && price < min) ok = false;
        if(!isNaN(max) && price > max) ok = false;
        card.style.display = ok ? '' : 'none';
        if(ok) shown++;
      });
      lemastore.toast('Filtre appliqué (maquette) : ' + shown + ' produit(s).');
    }
    function resetFilter(){
      ['filterBrand','filterMin','filterMax'].forEach(function(id){
        var el = document.getElementById(id);
        if(!el) return;
        if(el.tagName === 'SELECT') el.selectedIndex = 0;
        else el.value = '';
      });
      var cards = grid.querySelectorAll('.product-layout');
      cards.forEach(function(card){ card.style.display = ''; });
      lemastore.toast('Filtres réinitialisés (maquette).');
    }
    var applyBtn = document.getElementById('filterApply');
    var resetBtn = document.getElementById('filterReset');
    if(applyBtn) applyBtn.addEventListener('click', applyFilter);
    if(resetBtn) resetBtn.addEventListener('click', resetFilter);
  })();
</script>
@endsection
