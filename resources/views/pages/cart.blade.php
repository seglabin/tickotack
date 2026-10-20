@extends('layoutfront')
@section('title', 'Panier')

@php
  //dd($panier);
  $subtotal = 0;
 
@endphp

@section('content')
<div id="checkout-cart" class="container">
  <ul class="breadcrumb">
    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i></a></li>
    <li><a href="{{ route('cart') }}">Panier</a></li>
  </ul>

  <h1>Panier</h1>

  @if(session('error'))
    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
  @endif

  @if($panier == null)
    <p>Votre panier est vide.</p>
    <div class="buttons">
      <div class="pull-right"><a href="{{ route('home') }}" class="btn btn-primary">Continuer vos achats</a></div>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <td class="text-center"><strong>Image</strong></td>
            <td class="text-left"><strong>Produit</strong></td>
            <td class="text-left"><strong>Quantité</strong></td>
            <td class="text-right"><strong>Prix</strong></td>
            <td class="text-right"><strong>Montant</strong></td>
          </tr>
        </thead>
        <tbody>
          @foreach($panier as $idx => $p)
                   @php
                      $subtotal += $p['montant'];
                    @endphp
            <tr data-line="{{ $idx }}">
              <td class="text-center" style="width: 120px;">
                <a href="{{ route('product', ['slug' => $p['idproduit']]) }}"><img src="{{ asset($p['photo']) }}" alt="{{ $p['libelle'] }}" class="img-thumbnail" style="max-width: 90px;"></a>
              </td>
              <td class="text-left">
                <a href="{{ route('product', ['slug' => $p['idproduit']]) }}">{{ $p['libelle'] }}</a>
                <div class="help-block">SKU: LM-{{ $p['idproduit'] }} • Marque: {{ strtoupper($p['libmarque']) }}</div>
              </td>
              <td class="text-left" style="width: 200px;">
                <div class="input-group" style="max-width: 200px;">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="lemastore.qtyMinus('qte-{{ $idx }}'); majmontant('{{ $idx }}');"><i class="fa fa-minus"></i></button>
                  </span>
                  <input type="text" value="{{ $p['qte'] }}" class="form-control" id="qte-{{ $idx }}" data-price="{{ $p['pu'] }}">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="lemastore.qtyPlus('qte-{{ $idx }}'); majmontant('{{ $idx }}');"><i class="fa fa-plus"></i></button>
                  </span>
                  <span class="input-group-btn">
                    <button class="btn btn-danger" type="button" onclick="retraitdupanier({{ $p['idproduit'] }});"><i class="fa fa-times"></i></button>
                  </span>
                </div>
              </td>
              <td class="text-right"><span class="price">{{ number_format($p['pu'], 2, ',', ' ') }} </span></td>
              <td class="text-right"><span class="price" id="lineTotal-{{ $idx }}"> {{ number_format($p['montant'], 2, ',', ' ') }} </span></td>
           
              <?php
              
              ?>
              <input type="hidden" id="idproduit-{{ $idx }}" value = "{{$p['idproduit']}}">
              <input type="hidden" id="pu-{{ $idx }}" value = "{{$p['pu']}}">
              <input type="hidden" readonly id="montant-{{ $idx }}" value = "{{ $p['montant']}} "> 

            </tr>
          @endforeach
          @php
            $shipping = $subtotal > 0 ? 2.50 : 0;
            $tax = $subtotal > 0 ? round($subtotal * 0.18, 2) : 0;
            $total = $subtotal + $shipping + $tax;
           @endphp
          <input type="hidden" id="taille" value = "{{($idx  + 1 )}}">
          <input type="hidden" id="montLivraison" value = "{{($shipping )}}">
          <input type="hidden" id="montTaxe" value = "{{($tax )}}">
          <input type="hidden" id="montSubtotal" value = "">
        </tbody>
      </table>
    </div>

    <div class="row">
      <div class="col-sm-4 col-sm-offset-8">
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

    <div class="buttons clearfix">
      <div class="pull-left"><a href="{{ route('category') }}" class="btn btn-default">Continuer vos achats</a></div>
      <div class="pull-right"><a href="{{ route('checkout') }}" class="btn btn-primary">Commander</a></div>
    </div>
  @endif
</div>

<script>
// calcSubtotal();

function majmontant(idx) { 
  let pu = parseInt($('#pu-'+idx).val());  //alert(pu);
  let qte = parseInt($('#qte-'+idx).val());
  let mont = (pu*qte);
  $('#montant-'+idx).val(mont);
  document.getElementById('lineTotal-'+idx).innerHTML = formatmontant(mont);
  calcSubtotal();
  
  let idprod = $('#idproduit-'+idx).val();
  majsession(idprod,qte);
}

function calcSubtotal() {
  let n = $('#taille').val(); 
  let som = 0;
  for (let i = 0; i < n; i++) {
    som += parseInt($('#montant-'+i).val());
    
  }
  //alert(som);
  $('#montSubtotal').val(som);
  document.getElementById('cartSubtotal').innerHTML = formatmontant(som);
  calcTotal();
}

function calcTotal() {  
  let total = parseFloat($('#montSubtotal').val()) + parseFloat($('#montLivraison').val()) + parseFloat($('#montTaxe').val());
  document.getElementById('cartTotal').innerHTML = formatmontant(total);
}

function majsession(idprod, qte) { 

  $('#idproduitAjout').val(idprod);
  $('#qteAjout').val(qte);
 
   document.forms["formAddCart"].action = '/update-session-panier';
   document.forms["formAddCart"].submit();
    // fetch('/update-session-panier', {
    //   method : 'POST',
    //   headers : {
    //     'Content-Type': 'application/json',
    //     'X-CRSF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content')//Indispensable
    //   },
    //   body: JSON.stringify({value: 'nouvelle_valeur'})
    
    // }).then(response=> response.json())
    // .then(data => console.log(data));
  // alert('Merci Seigneur');
}


  // (function(){
  //   // Calcul panier côté client (maquette)
  //   lemastore.recalcCart = function(){
  //     var subtotal = 0;
  //     var rows = document.querySelectorAll('#checkout-cart tbody tr');
  //     rows.forEach(function(row){
  //       if(row.style.display === 'none') return;
  //       var line = row.getAttribute('data-line');
  //       var qtyEl = document.getElementById('qty-' + line);
  //       var price = parseFloat(qtyEl.getAttribute('data-price') || '0');
  //       var qty = parseInt((qtyEl.value || '0').replace(/\D/g,''), 10);
  //       if(isNaN(qty) || qty < 1) qty = 1;
  //       qtyEl.value = qty;
  //       var lineTotal = price * qty;
  //       subtotal += lineTotal;
  //       var lineEl = document.getElementById('lineTotal-' + line);
  //       if(lineEl) lineEl.textContent = lemastore.money(lineTotal);
  //     });
  //     var shipping = subtotal > 0 ? 2.50 : 0;
  //     var tax = subtotal > 0 ? Math.round(subtotal * 0.18 * 100) / 100 : 0;
  //     var total = subtotal + shipping + tax;
  //     document.getElementById('cartSubtotal').textContent = lemastore.money(subtotal);
  //     document.getElementById('cartShipping').textContent = lemastore.money(shipping);
  //     document.getElementById('cartTax').textContent = lemastore.money(tax);
  //     document.getElementById('cartTotal').textContent = lemastore.money(total);
  //   };

  //   lemastore.removeCartLine = function(line){
  //     var row = document.querySelector('#checkout-cart tbody tr[data-line="' + line + '"]');
  //     if(row) row.style.display = 'none';
  //     lemastore.recalcCart();
  //     lemastore.toast('Article supprimé (maquette).');
  //   };
  // })();
</script>
@endsection
