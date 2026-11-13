@extends('layoutback', ['titre' => $titre])

@section('content')
@php
    $dateDebutLabel = !empty($filters['date_debut']) ? date('d/m/Y', strtotime($filters['date_debut'])) : null;
    $dateFinLabel = !empty($filters['date_fin']) ? date('d/m/Y', strtotime($filters['date_fin'])) : null;
    $periodeLabel = $dateDebutLabel && $dateFinLabel
        ? $dateDebutLabel . ' au ' . $dateFinLabel
        : ($dateDebutLabel ? 'À partir du ' . $dateDebutLabel : ($dateFinLabel ? 'Jusqu’au ' . $dateFinLabel : 'Toutes les périodes'));
    $conteneurSelectionne = isset($conteneurs) ? $conteneurs->firstWhere('id', (int) ($filters['conteneur'] ?? 0)) : null;
    $colisSelectionne = isset($colis) ? $colis->firstWhere('id', (int) ($filters['colis'] ?? 0)) : null;
    $statutSelectionne = isset($statuts) ? $statuts->firstWhere('id', (int) ($filters['statut'] ?? 0)) : null;
@endphp

<style>
    .etat-report { margin: 0 auto; max-width: 1500px; }
    .etat-report__actions { display: flex; gap: 8px; margin-bottom: 18px; }
    .etat-report__filters { align-items: end; display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
    .etat-report__filters label { display: block; font-size: 12px; margin-bottom: 5px; }
    .etat-report__filters input, .etat-report__filters select { border: 1px solid #adb5bd; border-radius: 4px; font-size: 14px; min-height: 36px; padding: 6px 8px; }
    .etat-report__button { background: #0d6efd; border: 0; border-radius: 4px; color: #fff; cursor: pointer; display: inline-block; font-size: 14px; padding: 9px 13px; text-decoration: none; }
    .etat-report__button--secondary { background: #6c757d; }
    .etat-report__button--print { background: #198754; }
    .etat-report__table-wrap { overflow-x: auto; }
    .etat-report__table { border-collapse: collapse; width: 100%; }
    .etat-report__table th, .etat-report__table td { border: 1px solid #8b949e; padding: 8px; text-align: left; }
    .etat-report__table th { background: #eef2f5; }
    .etat-report__table td.number { text-align: right; white-space: nowrap; }
    .etat-report__empty { color: #52616b; text-align: center !important; }
    .etat-print-header, .etat-print-criteria { display: none; }

    @media print {
        @if(in_array($type, ['colis', 'transferts'], true))
        @page { size: A4 landscape; margin: 10mm; }
        @else
        @page { size: A4 portrait; margin: 12mm; }
        @endif

        body { background: #fff !important; color: #17202a !important; font-size: 10px; }
        body.back-office > #preloader,
        body.back-office > .scroll__top,
        body.back-office > header,
        body.back-office > footer { display: none !important; }
        main.fix { overflow: visible !important; }
        main.fix > form#myform,
        main.fix > form#myform > section.about__area-five,
        main.fix > form#myform > section.about__area-five > .container,
        main.fix > form#myform > section.about__area-five > .container > .row,
        main.fix > form#myform > section.about__area-five > .container > .row > .col-lg-12,
        main.fix .about__content-five { display: contents !important; }
        .section-title, .no-print { display: none !important; }
        .etat-report { margin: 0; max-width: none; width: 100%; }
        .etat-print-header { align-items: center; border-bottom: 2px solid #263746; display: flex; gap: 10mm; margin-bottom: 5mm; padding-bottom: 4mm; }
        .etat-print-header img { max-height: 22mm; max-width: 95mm; object-fit: contain; }
        .etat-print-header__details { flex: 1; text-align: right; }
        .etat-print-header__details strong { display: block; font-size: 15px; }
        .etat-print-header__details h1 { font-size: 17px; margin: 2mm 0; }
        .etat-print-header__details p { color: #52616b; margin: 0; }
        .etat-print-criteria { border-bottom: 1px solid #d5dadd; display: flex; flex-wrap: wrap; gap: 4mm 10mm; margin-bottom: 5mm; padding-bottom: 3mm; }
        .etat-report__table-wrap { overflow: visible; }
        .etat-report__table { font-size: 9px; table-layout: auto; }
        .etat-report__table th, .etat-report__table td { padding: 5px; }
        .etat-report__table th { background: #eef2f5 !important; print-color-adjust: exact; }
        .etat-report__table tr { page-break-inside: avoid; }
    }
</style>

<div class="etat-report">
    <div class="etat-report__actions no-print">
        <button type="button" class="etat-report__button etat-report__button--secondary" onclick="history.back()">Retour</button>
        <button type="button" class="etat-report__button etat-report__button--print" onclick="window.print()">Imprimer</button>
    </div>

    <div class="etat-print-header">
        <img src="{{ asset('assets/images/entete.png') }}" alt="Global Vision">
        <div class="etat-print-header__details">
            <strong>GLOBAL VISION</strong>
            <h1>{{ $titre }}</h1>
            <p>Imprimé le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
    <div class="etat-print-criteria">
        <span><strong>Période :</strong> {{ $periodeLabel }}</span>
        @if(isset($conteneurs))
            <span><strong>Conteneur :</strong> {{ $conteneurSelectionne->numconteneur ?? 'Tous' }}</span>
        @endif
        @if(isset($statuts))
            <span><strong>Statut :</strong> {{ $statutSelectionne->libelle ?? 'Tous' }}</span>
        @endif
        @if(isset($colis))
            <span><strong>Colis :</strong> {{ $colisSelectionne->code ?? 'Tous' }}</span>
        @endif
    </div>

    <div class="etat-report__filters no-print" id="etats-filtres"
        onkeydown="if (event.key === 'Enter') { event.preventDefault(); appliquerFiltresEtats('{{ route('etats.' . $type) }}'); }">
        <div>
            <label for="date_debut">Période début</label>
            <input id="date_debut" type="date" name="date_debut" value="{{ $filters['date_debut'] ?? '' }}">
        </div>
        <div>
            <label for="date_fin">Période fin</label>
            <input id="date_fin" type="date" name="date_fin" value="{{ $filters['date_fin'] ?? '' }}">
        </div>
        @if($type === 'colis')
            <div>
                <label for="conteneur">Conteneur</label>
                <select id="conteneur" name="conteneur">
                    <option value="">Tous les conteneurs</option>
                    @foreach($conteneurs as $conteneur)
                        <option value="{{ $conteneur->id }}" @selected(($filters['conteneur'] ?? '') == $conteneur->id)>{{ $conteneur->numconteneur }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="statut">Statut</label>
                <select id="statut" name="statut">
                    <option value="">Tous les statuts</option>
                    @foreach($statuts as $statut)
                        <option value="{{ $statut->id }}" @selected(($filters['statut'] ?? '') == $statut->id)>{{ $statut->libelle }}</option>
                    @endforeach
                </select>
            </div>
        @elseif($type === 'paiements')
            <div>
                <label for="conteneur">Conteneur</label>
                <select id="conteneur" name="conteneur">
                    <option value="">Tous les conteneurs</option>
                    @foreach($conteneurs as $conteneur)
                        <option value="{{ $conteneur->id }}" @selected(($filters['conteneur'] ?? '') == $conteneur->id)>{{ $conteneur->numconteneur }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="colis">Colis</label>
                <select id="colis" name="colis">
                    <option value="">Tous les colis</option>
                    @foreach($colis as $expedition)
                        <option value="{{ $expedition->id }}" @selected(($filters['colis'] ?? '') == $expedition->id)>{{ $expedition->code }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <button type="button" class="etat-report__button" onclick="appliquerFiltresEtats('{{ route('etats.' . $type) }}')">Filtrer</button>
        <a href="{{ route('etats.' . $type) }}" class="etat-report__button etat-report__button--secondary">Réinitialiser</a>
    </div>

    <div class="etat-report__table-wrap">
        <table class="etat-report__table">
            <thead>
            <tr>
                @if($type === 'colis')
                    <th>N°</th><th>Expéditeur</th><th>Récepteur</th><th>N° Récepteur</th><th>Trajet</th><th>Statut</th><th>Conteneur</th><th>Montant</th><th>Total payé</th><th>Reste à payer</th><th class="no-print">Paiements</th>
                @elseif($type === 'paiements')
                    <th>Date du paiement</th><th>Référence colis</th><th>Montant</th>
                @elseif($type === 'transferts')
                    <th>N°</th><th>Date</th><th>Code</th><th>Expéditeur</th><th>Trajet</th><th>Montant Dépôt</th><th>Montant Retrait</th><th class="no-print">Actions</th>
                @else
                    <th>N°</th><th>Date</th><th>Libellé</th><th>Montant</th><th>Caisse</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @forelse($donnees as $donnee)
                <tr>
                    @if($type === 'colis')
                        <td>{{ $donnee->code }}</td>
                        <td>{{ $donnee->expediteur ?: '-' }}</td>
                        <td>{{ $donnee->recepteur ?: '-' }}</td>
                        <td>{{ $donnee->numrecepteur ?: '-' }}</td>
                        <td>{{ $donnee->villedepart ?: '-' }} - {{ $donnee->villedestination ?: '-' }}</td>
                        <td>{{ $donnee->statut ?: '-' }}</td>
                        <td>{{ $donnee->conteneur ?: '-' }}</td>
                        <td class="number">{{ number_format((float) $donnee->montant, 0, ',', ' ') }}</td>
                        <td class="number">{{ number_format((float) $donnee->totalpaye, 0, ',', ' ') }}</td>
                        <td class="number">{{ number_format(max(0, (float) $donnee->montant - (float) $donnee->totalpaye), 0, ',', ' ') }}</td>
                        <td class="no-print"><a class="etat-report__button" href="{{ route('etats.paiements', ['colis' => $donnee->id]) }}">Paiements</a></td>
                    @elseif($type === 'paiements')
                        <td>{{ date('d/m/Y H:i', strtotime($donnee->payment_date)) }}</td>
                        <td>{{ $donnee->code }}</td>
                        <td class="number">{{ number_format((float) $donnee->montant, 0, ',', ' ') }}</td>
                    @elseif($type === 'transferts')
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('d/m/Y H:i', strtotime($donnee->datetransfert)) }}</td>
                        <td>{{ $donnee->code }}</td>
                        <td>{{ $donnee->expediteur ?: '-' }}</td>
                        <td>{{ $donnee->villedepart ?: '-' }} - {{ $donnee->villedestination ?: '-' }}</td>
                        <td class="number">{{ number_format((float) $donnee->montdepot, 0, ',', ' ') }}</td>
                        <td class="number">{{ number_format((float) $donnee->montretrait, 0, ',', ' ') }}</td>
                        <td class="no-print"><a class="etat-report__button" href="{{ url('transfert/' . $donnee->id . '/recu') }}" target="_blank" rel="noopener">Reçu</a></td>
                    @else
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('d/m/Y H:i', strtotime($donnee->date)) }}</td>
                        <td>{{ $donnee->libelle }}</td>
                        <td class="number">{{ number_format((float) $donnee->montant, 0, ',', ' ') }}</td>
                        <td>{{ $donnee->caisse ?: '-' }}</td>
                    @endif
                </tr>
            @empty
                <tr><td class="etat-report__empty" colspan="{{ $type === 'colis' ? 11 : ($type === 'paiements' ? 3 : ($type === 'transferts' ? 8 : 5)) }}">Aucun résultat pour ces critères.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function appliquerFiltresEtats(url) {
        var parametres = new URLSearchParams();
        document.querySelectorAll('#etats-filtres input[name], #etats-filtres select[name]').forEach(function (champ) {
            if (champ.value) {
                parametres.set(champ.name, champ.value);
            }
        });
        window.location.assign(url + (parametres.toString() ? '?' + parametres.toString() : ''));
    }
</script>
@endsection
