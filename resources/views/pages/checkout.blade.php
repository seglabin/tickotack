@extends('layoutfront')
@section('title', 'Finaliser la commande')

@section('content')
<div id="checkout-checkout" class="container checkout-page">
  <ul class="breadcrumb">
    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i></a></li>
    <li><a href="{{ route('cart') }}">Panier</a></li>
    <li>Commande</li>
  </ul>

  <h1>Finaliser la commande</h1>
  <p class="checkout-intro">Renseignez vos coordonnées et vérifiez votre commande avant de la confirmer.</p>

  @if($errors->any())
    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
  @endif
  @if($typesLivraison->isEmpty())
    <div class="alert alert-warning" role="alert">
      Aucun mode de livraison n’est configuré. Contactez-nous pour finaliser votre commande.
    </div>
  @endif

  <form method="POST" action="{{ route('checkout.place-order') }}" id="checkout-form">
    @csrf
    <div class="row">
      <div class="col-sm-8">
        <section class="panel panel-default checkout-panel">
          <div class="panel-heading"><strong>1. Vos coordonnées</strong></div>
          <div class="panel-body">
            <div class="form-group required">
              <label for="checkout-name">Nom complet</label>
              <input class="form-control" type="text" id="checkout-name" name="nom" value="{{ old('nom') }}" autocomplete="name" maxlength="255" required>
            </div>
            <div class="row">
              <div class="col-sm-6 form-group required">
                <label for="checkout-email">Adresse e-mail</label>
                <input class="form-control" type="email" id="checkout-email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required>
              </div>
              <div class="col-sm-6 form-group required">
                <label for="checkout-phone">Téléphone</label>
                <input class="form-control" type="tel" id="checkout-phone" name="telephone" value="{{ old('telephone') }}" autocomplete="tel" maxlength="20" required>
              </div>
            </div>
          </div>
        </section>

        <section class="panel panel-default checkout-panel">
          <div class="panel-heading"><strong>2. Livraison ou retrait</strong></div>
          <div class="panel-body">
            @if($typesLivraison->isNotEmpty())
              <div class="form-group required">
                <label for="checkout-delivery">Mode de réception</label>
                <select class="form-control" id="checkout-delivery" name="idtypelivraison" required>
                  @foreach($typesLivraison as $type)
                    <option value="{{ $type->id }}" data-pickup="{{ \Illuminate\Support\Str::lower(trim($type->libelle)) === 'moi même' ? '1' : '0' }}" {{ (string) old('idtypelivraison', $typesLivraison->first()->id) === (string) $type->id ? 'selected' : '' }}>
                      {{ $type->libelle }}
                    </option>
                  @endforeach
                </select>
              </div>
            @endif

            <div id="delivery-address-fields">
              <div class="form-group required">
                <label for="checkout-address">Adresse</label>
                <input class="form-control" type="text" id="checkout-address" name="adresse" value="{{ old('adresse') }}" autocomplete="street-address" maxlength="255">
              </div>
              <div class="row">
                <div class="col-sm-6 form-group required">
                  <label for="checkout-city">Ville</label>
                  <input class="form-control" type="text" id="checkout-city" name="ville" value="{{ old('ville') }}" autocomplete="address-level2" maxlength="255">
                </div>
                <div class="col-sm-6 form-group required">
                  <label for="checkout-country">Pays</label>
                  <input class="form-control" type="text" id="checkout-country" name="pays" value="{{ old('pays', 'Bénin') }}" autocomplete="country-name" maxlength="100">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="checkout-notes">Instructions complémentaires <span class="text-muted">(facultatif)</span></label>
              <textarea class="form-control" id="checkout-notes" name="observation" rows="3" maxlength="2000">{{ old('observation') }}</textarea>
            </div>
          </div>
        </section>

        <section class="panel panel-default checkout-panel">
          <div class="panel-heading"><strong>3. Paiement</strong></div>
          <div class="panel-body">
            <div class="checkout-payment-choice">
              <i class="fa fa-money" aria-hidden="true"></i>
              <div>
                <strong>Paiement à la livraison</strong>
                <p>Vous réglerez le montant total lors de la réception de votre commande.</p>
              </div>
            </div>
            <p class="help-block">Le paiement par carte et Mobile Money n’est pas disponible.</p>
          </div>
        </section>
      </div>

      <aside class="col-sm-4">
        <section class="panel panel-default checkout-panel checkout-summary">
          <div class="panel-heading"><strong>Récapitulatif</strong></div>
          <div class="panel-body">
            <div class="checkout-items">
              @foreach($panier as $item)
                <div class="checkout-item">
                  <div>
                    <strong>{{ $item['libelle'] }}</strong>
                    <span>{{ $item['qte'] }} × {{ number_format($item['pu'], 2, ',', ' ') }} €</span>
                  </div>
                  <strong>{{ number_format($item['montant'], 2, ',', ' ') }} €</strong>
                </div>
              @endforeach
            </div>
            <div class="checkout-total-line"><span>Sous-total</span><strong>{{ number_format($subtotal, 2, ',', ' ') }} €</strong></div>
            <div class="checkout-total-line"><span>Livraison</span><strong id="checkout-shipping">{{ number_format($typesLivraison->isNotEmpty() && \Illuminate\Support\Str::lower(trim($typesLivraison->first()->libelle)) === 'moi même' ? 0 : 2.50, 2, ',', ' ') }} €</strong></div>
            <div class="checkout-total-line"><span>TVA estimative (18 %)</span><strong id="checkout-tax">{{ number_format(round($subtotal * 0.18, 2), 2, ',', ' ') }} €</strong></div>
            <div class="checkout-total-line checkout-grand-total">
              <span>Total estimé</span>
              <strong id="checkout-total">{{ number_format($subtotal + ($typesLivraison->isNotEmpty() && \Illuminate\Support\Str::lower(trim($typesLivraison->first()->libelle)) === 'moi même' ? 0 : 2.50) + round($subtotal * 0.18, 2), 2, ',', ' ') }} €</strong>
            </div>
            <p class="checkout-estimate-note">Les frais et la TVA sont estimatifs et seront confirmés par notre équipe.</p>
            <button class="btn btn-primary btn-block btn-lg checkout-submit" type="submit" {{ $typesLivraison->isEmpty() ? 'disabled' : '' }}>
              Confirmer ma commande <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </button>
            <a href="{{ route('cart') }}" class="btn btn-default btn-block">Retour au panier</a>
            <p class="checkout-safe"><i class="fa fa-lock" aria-hidden="true"></i> Vos informations sont utilisées pour traiter votre commande.</p>
          </div>
        </section>
      </aside>
    </div>
  </form>
</div>
@endsection

@push('head')
<style>
  .checkout-page {
    max-width: 1120px;
    padding-bottom: 40px;
    color: #25332b;
  }

  .checkout-page h1 {
    margin-bottom: 8px;
    font-size: 28px;
    font-weight: 700;
  }

  .checkout-intro {
    margin-bottom: 24px;
    color: #6d7b72;
  }

  .checkout-panel {
    margin-bottom: 18px;
    border-color: #e5ebe6;
    border-radius: 7px;
    box-shadow: 0 3px 14px rgba(26, 63, 39, .04);
  }

  .checkout-panel > .panel-heading {
    padding: 14px 18px;
    border-color: #e5ebe6;
    background: #f7f9f7;
  }

  .checkout-panel > .panel-body {
    padding: 18px;
  }

  .checkout-page .form-group {
    margin-bottom: 16px;
  }

  .checkout-page label {
    margin-bottom: 6px;
    font-weight: 600;
  }

  .checkout-page .form-control {
    min-height: 42px;
    border-color: #dce5de;
    border-radius: 4px;
    box-shadow: none;
  }

  .checkout-page textarea.form-control {
    min-height: 86px;
  }

  .checkout-payment-choice {
    display: flex;
    align-items: flex-start;
    gap: 13px;
  }

  .checkout-payment-choice > i {
    color: #287a48;
    font-size: 20px;
  }

  .checkout-payment-choice p {
    margin: 5px 0 0;
    color: #6d7b72;
  }

  .checkout-items {
    margin-bottom: 12px;
  }

  .checkout-item,
  .checkout-total-line {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #edf1ed;
  }

  .checkout-item > div strong,
  .checkout-item > div span {
    display: block;
  }

  .checkout-item > div span,
  .checkout-total-line > span {
    color: #6d7b72;
    font-size: 13px;
  }

  .checkout-item > strong,
  .checkout-total-line > strong {
    white-space: nowrap;
  }

  .checkout-grand-total {
    align-items: center;
    border-bottom: 0;
    color: #185333;
    font-size: 17px;
  }

  .checkout-estimate-note,
  .checkout-safe {
    color: #77847a;
    font-size: 12px;
    line-height: 1.5;
  }

  .checkout-estimate-note {
    margin: 0 0 16px;
  }

  .checkout-safe {
    margin: 14px 0 0;
    text-align: center;
  }

  .checkout-safe i {
    margin-right: 4px;
  }

  .checkout-submit {
    padding: 12px;
    border-color: #287a48;
    background: #287a48;
    font-weight: 700;
  }

  .checkout-submit:hover,
  .checkout-submit:focus {
    border-color: #185333;
    background: #185333;
  }

  @media (max-width: 767px) {
    .checkout-page h1 {
      font-size: 24px;
    }

    .checkout-summary {
      margin-top: 4px;
    }
  }
</style>
@endpush

@push('scripts')
<script>
  $(function () {
    var delivery = $('#checkout-delivery');
    var addressFields = $('#delivery-address-fields');
    var addressInputs = addressFields.find('input');
    var subtotal = {{ json_encode($subtotal) }};

    function updateDeliverySummary() {
      var pickup = delivery.find(':selected').data('pickup') === 1 || delivery.find(':selected').data('pickup') === '1';
      var shipping = pickup ? 0 : 2.50;
      var tax = Math.round(subtotal * 0.18 * 100) / 100;

      addressFields.toggle(!pickup);
      addressInputs.prop('required', !pickup);
      $('#checkout-shipping').text(shipping.toFixed(2).replace('.', ',') + ' €');
      $('#checkout-tax').text(tax.toFixed(2).replace('.', ',') + ' €');
      $('#checkout-total').text((subtotal + shipping + tax).toFixed(2).replace('.', ',') + ' €');
    }

    delivery.on('change', updateDeliverySummary);
    updateDeliverySummary();

    $('#checkout-form').on('submit', function () {
      $(this).find('.checkout-submit').prop('disabled', true).text('Enregistrement de la commande…');
    });
  });
</script>
@endpush
