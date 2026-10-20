@extends('layoutfront')
@section('title', 'Commande confirmée')

@section('content')
<main class="container order-confirmation">
  <ul class="breadcrumb">
    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i></a></li>
    <li><a href="{{ route('category') }}">Catalogue</a></li>
    <li>Commande confirmée</li>
  </ul>

  <div class="order-confirmation-card">
    <div class="order-confirmation-icon"><i class="fa fa-check" aria-hidden="true"></i></div>
    <p class="order-confirmation-eyebrow">Merci {{ $commande->nomclient }}</p>
    <h1>Votre commande est enregistrée</h1>
    <p class="order-confirmation-intro">Notre équipe vous contactera pour confirmer les détails de livraison et le montant final.</p>
    <div class="order-reference">
      <span>Référence de commande</span>
      <strong>{{ $commande->reference }}</strong>
    </div>

    <div class="row text-left order-confirmation-details">
      <div class="col-sm-6">
        <h2>Réception</h2>
        <p><strong>{{ $commande->typelivraison }}</strong></p>
        @if($commande->adresse_livraison)
          <p>{{ $commande->adresse_livraison }}<br>{{ $commande->ville_livraison }}, {{ $commande->pays_livraison }}</p>
        @endif
        <p>{{ $commande->telephoneclient }}<br>{{ $commande->emailclient }}</p>
        <p>Paiement à la livraison</p>
      </div>
      <div class="col-sm-6">
        <h2>Articles commandés</h2>
        @foreach($articles as $article)
          <div class="order-confirmation-item">
            <span>{{ $article->libelle ?? 'Article' }} × {{ $article->qte }}</span>
            <strong>{{ number_format($article->pu * $article->qte, 2, ',', ' ') }} €</strong>
          </div>
        @endforeach
        <div class="order-confirmation-item"><span>Sous-total</span><strong>{{ number_format($commande->sous_total, 2, ',', ' ') }} €</strong></div>
        <div class="order-confirmation-item"><span>Livraison estimée</span><strong>{{ number_format($commande->frais_livraison, 2, ',', ' ') }} €</strong></div>
        <div class="order-confirmation-item"><span>TVA estimative</span><strong>{{ number_format($commande->taxe, 2, ',', ' ') }} €</strong></div>
        <div class="order-confirmation-item order-confirmation-total"><span>Total estimé</span><strong>{{ number_format($commande->total, 2, ',', ' ') }} €</strong></div>
      </div>
    </div>

    <div class="order-confirmation-actions">
      <a href="{{ route('category') }}" class="btn btn-primary">Continuer vos achats</a>
      <a href="{{ route('home') }}" class="btn btn-default">Retour à l’accueil</a>
    </div>
  </div>
</main>
@endsection

@push('head')
<style>
  .order-confirmation {
    max-width: 1000px;
    padding-bottom: 45px;
  }

  .order-confirmation-card {
    padding: 34px;
    border: 1px solid #e5ebe6;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 5px 24px rgba(26, 63, 39, .06);
    text-align: center;
  }

  .order-confirmation-icon {
    display: flex;
    width: 58px;
    height: 58px;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    border-radius: 50%;
    background: #eaf5ec;
    color: #287a48;
    font-size: 25px;
  }

  .order-confirmation-eyebrow {
    margin: 0;
    color: #287a48;
    font-weight: 700;
  }

  .order-confirmation h1 {
    margin: 8px 0;
    color: #25332b;
    font-size: 28px;
    font-weight: 700;
  }

  .order-confirmation-intro {
    max-width: 620px;
    margin: 0 auto 18px;
    color: #6d7b72;
    line-height: 1.6;
  }

  .order-reference {
    display: inline-flex;
    flex-direction: column;
    gap: 4px;
    padding: 10px 18px;
    border-radius: 5px;
    background: #f5f8f5;
  }

  .order-reference span {
    color: #6d7b72;
    font-size: 12px;
  }

  .order-reference strong {
    color: #185333;
    overflow-wrap: anywhere;
  }

  .order-confirmation-details {
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid #e7ede8;
  }

  .order-confirmation-details h2 {
    margin: 0 0 14px;
    font-size: 17px;
    font-weight: 700;
  }

  .order-confirmation-details p {
    color: #536359;
    line-height: 1.6;
  }

  .order-confirmation-item {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    padding: 8px 0;
    border-bottom: 1px solid #edf1ed;
    color: #536359;
    text-align: left;
  }

  .order-confirmation-item strong {
    color: #25332b;
    white-space: nowrap;
  }

  .order-confirmation-total {
    color: #185333;
    font-weight: 700;
  }

  .order-confirmation-total strong {
    color: #185333;
    font-size: 17px;
  }

  .order-confirmation-actions {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 24px;
  }

  @media (max-width: 767px) {
    .order-confirmation-card {
      padding: 24px 16px;
    }

    .order-confirmation h1 {
      font-size: 23px;
    }
  }
</style>
@endpush
