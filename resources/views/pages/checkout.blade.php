@extends('layoutfront')
@section('title', 'Commande')

@php
  
  $subtotal = 0;
 /* foreach($items as $it){
    $subtotal += ($it['product']['price'] ?? 0) * ($it['qty'] ?? 0);
  }*/
  $shipping = $subtotal > 0 ? 2.50 : 0;
  $tax = $subtotal > 0 ? round($subtotal * 0.18, 2) : 0;
  $total = $subtotal + $shipping + $tax;
@endphp

@section('content')
<div id="checkout-checkout" class="container">
  <ul class="breadcrumb">
    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i></a></li>
    <li><a href="{{ route('cart') }}">Panier</a></li>
    <li><a href="{{ route('checkout') }}">Commande</a></li>
  </ul>

  <h1>Finaliser la commande</h1>

  <div class="row">
    <div class="col-sm-8">
      <div class="panel panel-default">
        <div class="panel-heading"><strong>Informations client</strong></div>
        <div class="panel-body">
          <form class="form-horizontal" onsubmit="return false;">
            <div class="form-group required">
              <label class="control-label col-sm-3">Nom</label>
              <div class="col-sm-9"><input class="form-control" type="text" placeholder="Votre nom"></div>
            </div>
            <div class="form-group required">
              <label class="control-label col-sm-3">Email</label>
              <div class="col-sm-9"><input class="form-control" type="email" placeholder="email@exemple.com"></div>
            </div>
            <div class="form-group required">
              <label class="control-label col-sm-3">Téléphone</label>
              <div class="col-sm-9"><input class="form-control" type="text" placeholder="+229 ..."></div>
            </div>
          </form>
          <p class="help-block">Formulaire statique (maquette).</p>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading"><strong>Adresse de livraison</strong></div>
        <div class="panel-body">
          <form class="form-horizontal" onsubmit="return false;">
            <div class="form-group required">
              <label class="control-label col-sm-3">Adresse</label>
              <div class="col-sm-9"><input class="form-control" type="text" placeholder="Rue, quartier"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-3">Ville</label>
              <div class="col-sm-9"><input class="form-control" type="text" placeholder="Cotonou"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-3">Pays</label>
              <div class="col-sm-9">
                <select class="form-control">
                  <option>Bénin</option>
                  <option>Togo</option>
                  <option>Burkina Faso</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-3">Notes</label>
              <div class="col-sm-9"><textarea class="form-control" rows="3" placeholder="Instructions de livraison"></textarea></div>
            </div>
          </form>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading"><strong>Paiement</strong></div>
        <div class="panel-body">
          <div class="radio">
            <label><input type="radio" name="pay" checked> Paiement à la livraison</label>
          </div>
          <div class="radio">
            <label><input type="radio" name="pay"> Mobile Money (maquette)</label>
          </div>
          <div class="radio">
            <label><input type="radio" name="pay"> Carte bancaire (maquette)</label>
          </div>
          <p class="help-block">Le paiement n’est pas connecté (maquette).</p>
        </div>
      </div>
    </div>

    <div class="col-sm-4">
      <div class="panel panel-default">
        <div class="panel-heading"><strong>Récapitulatif</strong></div>
        <div class="panel-body">
          @if($panier == null)
            <p>Votre panier est vide.</p>
            <a href="{{ route('category') }}" class="btn btn-primary btn-block">Voir le catalogue</a>
          @else
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr><td>Produit</td><td class="text-right">Total</td></tr>
                </thead>
                <tbody>
                  @foreach($panier as $p)
                    @php
                      $subtotal += $p['montant'];
                    @endphp
                    <tr>
                      <td>{{ $p['libelle'] }} <span class="text-muted">x{{ $p['qte'] }}</span></td>
                      <td class="text-right">{{ number_format($p['montant'], 2, ',', ' ') }} €</td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  @php
                    $shipping = $subtotal > 0 ? 2.50 : 0;
                    $tax = $subtotal > 0 ? round($subtotal * 0.18, 2) : 0;
                    $total = $subtotal + $shipping + $tax;  
                    @endphp
                  <tr><td class="text-right"><strong>Sous-total</strong></td><td class="text-right">{{ number_format($subtotal, 2, ',', ' ') }} €</td></tr>
                  <tr><td class="text-right"><strong>Livraison</strong></td><td class="text-right">{{ number_format($shipping, 2, ',', ' ') }} €</td></tr>
                  <tr><td class="text-right"><strong>TVA</strong></td><td class="text-right">{{ number_format($tax, 2, ',', ' ') }} €</td></tr>
                  <tr><td class="text-right"><strong>Total</strong></td><td class="text-right"><strong>{{ number_format($total, 2, ',', ' ') }} €</strong></td></tr>
                </tfoot>
              </table>
            </div>
            <button class="btn btn-primary btn-block btn-lg" type="button" onclick="lemastore.toast('Commande validée (maquette).')">Passer la commande</button>
            <a href="{{ route('cart') }}" class="btn btn-default btn-block">Retour au panier</a>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
