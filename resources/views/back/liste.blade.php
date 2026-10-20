{{--
@extends('layout-conculting', ['titre'=>$titre])
--}}
@extends('layoutback', ['titre'=>$titre])
@section('content')
@php

$config = (session('config') != null) ? session('config') : null;
$retour = null;
if(empty($conteneurs)) {
    $conteneurs = array();
}


@endphp

<div class="card-body">
  @switch($config)
  @case('transfert')
  <form method="GET" action="{{ request()->routeIs('transfert.retraits') ? route('transfert.retraits') : route('transfert') }}" class="row g-3 align-items-end mb-4">
    <div class="row g-3 align-items-end mb-4">
      <div class="col-md-3">
        <label for="depart" class="form-label">Départ</label>
        <select name="depart" id="depart" class="form-select">
          <option value="">Tous les départs</option>
          @foreach($adresses as $adresse)
          <option value="{{ $adresse->id }}" @selected(request('depart')==$adresse->id)>{{ $adresse->libelle }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label for="destination" class="form-label">Destination</label>
        <select name="destination" id="destination" class="form-select">
          <option value="">Toutes les destinations</option>
          @foreach($adresses as $adresse)
          <option value="{{ $adresse->id }}" @selected(request('destination')==$adresse->id)>{{ $adresse->libelle }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label for="date_debut" class="form-label">Période début</label>
        <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ request('date_debut') }}">
      </div>
      <div class="col-md-2">
        <label for="date_fin" class="form-label">Période fin</label>
        <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ request('date_fin') }}">
      </div>
      <div class="col-md-2">
        <label for="statut" class="form-label">Statut</label>
        <select name="statut" id="statut" class="form-select">
          <option value="">Tous</option>
          <option value="encours" @selected(request('statut')==='encours' )>En cours</option>
          <option value="recu" @selected(request('statut')==='recu' )>Reçu</option>
        </select>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <a href="{{ request()->routeIs('transfert.retraits') ? route('transfert.retraits') : route('transfert') }}" class="btn btn-outline-secondary">Réinitialiser</a>
      </div>
    </div>
  </form>
  @break
  @case('expedition')
  <form method="GET" action="{{ request()->routeIs('expedition.retraits') ? route('expedition.retraits') : route('expedition') }}" class="row g-3 align-items-end mb-4">
    <div class="row g-3 align-items-end mb-4">
      <div class="col-md-3">
        <div class="form-group">
          <label for="etat" class="form-label">Statut</label>
          @php
          echo chargerCombo($etats, 'id', 'libelle', 'etat', 'Tous les statuts', '', '', request('etat'));
          @endphp
        </div>
      </div>

      <div class="col-md-2">
        <div class="form-group">
          <label for="moyentransport" class="form-label">Moyen de transport</label>
          @php
          echo chargerCombo($moyentransports, 'id', 'libelle', 'moyentransport', 'Tous les moyens', '', '', request('moyentransport'));
          @endphp
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label for="conteneur" class="form-label">Conteneur</label>
           @php
          echo chargerCombo($conteneurs, 'id', 'libelle', 'conteneur', 'Tous les conteneurs', '', '', request('conteneur'));
          @endphp          
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label for="depart" class="form-label">Départ</label>
          @php
          echo chargerCombo($adresses, 'id', 'libelle', 'depart', 'Tous les départs', '', '', request('depart'));
          @endphp
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label for="destination" class="form-label">Destination</label>
          @php
          echo chargerCombo($adresses, 'id', 'libelle', 'destination', 'Toutes les destinations', '', '', request('destination'));
          @endphp
        </div>
      </div>

      <div class="col-md-2">
        <div class="form-group">
          <label for="date_debut" class="form-label">Période début</label>
          <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ request('date_debut') }}">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="date_fin" class="form-label">Période fin</label>
          <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ request('date_fin') }}">
        </div>
      </div>
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary">Filtrer</button>
      <a href="{{ request()->routeIs('expedition.retraits') ? route('expedition.retraits') : route('expedition') }}" class="btn btn-outline-secondary">Réinitialiser</a>
    </div>
  </form>
  @break
  @endswitch




  @if(count($donnees) > 0)
  @php
  tableau_html($donnees->toArray(), $entete, $cols, $btn);
  @endphp

  @endif

  @if($config === 'conteneur')
  <div class="modal fade" id="modalExpeditionsConteneur" tabindex="-1" aria-labelledby="modalExpeditionsConteneurLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="width: min(calc(100vw - 2rem), 1600px); max-width: 1600px;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalExpeditionsConteneurLabel">Les Colis du conteneur/Cargo </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <div id="expeditionsConteneurChargement" class="text-center py-3 d-none">Chargement des colis...</div>
          <div id="expeditionsConteneurMessage" class="alert d-none" role="status"></div>
          <div id="expeditionsConteneurContenu" class="table-responsive"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function afficherExpeditionsConteneur(id) {
      const modalElement = document.getElementById('modalExpeditionsConteneur');
      const chargement = document.getElementById('expeditionsConteneurChargement');
      const message = document.getElementById('expeditionsConteneurMessage');
      const contenu = document.getElementById('expeditionsConteneurContenu');
      const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
      chargement.classList.remove('d-none');
      message.className = 'alert d-none';
      contenu.replaceChildren();
      modal.show();

      fetch('/conteneur/' + encodeURIComponent(id) + '/expeditions', {
        headers: { 'Accept': 'application/json' }
      })
        .then(function (response) {
          if (!response.ok) throw new Error('Impossible de charger les expéditions.');
          return response.json();
        })
        .then(function (donnees) {
          const informations = document.createElement('div');
          informations.className = 'row g-3 mb-3';
          [
            ['Référence', donnees.conteneur.reference],
            ['Moyen de transport', donnees.conteneur.moyentransport],
            ["Type d'envoi", donnees.conteneur.typenvoi]
          ].forEach(function (information) {
            const colonne = document.createElement('div');
            colonne.className = 'col-md-4';
            const bloc = document.createElement('div');
            bloc.className = 'border rounded p-3 h-100';
            const libelle = document.createElement('div');
            libelle.className = 'text-muted small';
            libelle.textContent = information[0];
            const valeur = document.createElement('strong');
            valeur.className = 'd-block';
            valeur.textContent = information[1] || '-';
            bloc.append(libelle, valeur);
            colonne.appendChild(bloc);
            informations.appendChild(colonne);
          });
          contenu.appendChild(informations);

          const expeditions = donnees.expeditions;
          if (expeditions.length === 0) {
            const aucuneExpedition = document.createElement('div');
            aucuneExpedition.className = 'alert alert-info mb-0';
            aucuneExpedition.textContent = 'Aucune expédition n’est rattachée à ce conteneur.';
            contenu.appendChild(aucuneExpedition);
            return;
          }

          const colonnes = [
            ['code', 'Code'], ['datenreg', 'Date'], ['expediteur', 'Expéditeur'],
            ['recepteur', 'Récepteur'], ['villedepart', 'Départ'],
            ['villedestination', 'Destination'], ['nbrcolis', 'Colis'],
            ['poids', 'Poids'], ['etat', 'État']
          ];
          const table = document.createElement('table');
          table.className = 'table table-bordered table-striped align-middle mb-0';
          const thead = table.createTHead();
          const headerRow = thead.insertRow();
          colonnes.forEach(function (colonne) {
            const cellule = document.createElement('th');
            cellule.scope = 'col';
            cellule.textContent = colonne[1];
            headerRow.appendChild(cellule);
          });

          const tbody = table.createTBody();
          expeditions.forEach(function (expedition) {
            const ligne = tbody.insertRow();
            colonnes.forEach(function (colonne) {
              ligne.insertCell().textContent = expedition[colonne[0]] ?? '';
            });
          });
          contenu.appendChild(table);
        })
        .catch(function (error) {
          message.textContent = error.message;
          message.className = 'alert alert-danger';
        })
        .finally(function () {
          chargement.classList.add('d-none');
        });
    }
  </script>
  @endif


</div>

@endsection

@section('modals')
@if($config === 'expedition')
<div class="modal fade" id="modalPaiementColis" tabindex="-1" aria-labelledby="modalPaiementColisLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPaiementColisLabel">Paiement du colis</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <div id="paiementColisDetails" class="alert alert-info"></div>
        <div id="alertPaiementColis" class="alert d-none" role="alert"></div>
        <form id="formPaiementColis" method="POST" action="{{ route('paymentcolis.enregistrer') }}">
          @csrf
          <input type="hidden" id="paiement_idcolis" name="idcolis">
          <div class="mb-3">
            <label for="paiement_montant" class="form-label">Montant payé (CFA) (*)</label>
            <input type="number" class="form-control" id="paiement_montant" name="montant" min="0.01" step="0.01" required>
          </div>
          <div class="mb-3">
            <label for="paiement_date" class="form-label">Date du paiement (*)</label>
            <input type="datetime-local" class="form-control" id="paiement_date" name="payment_date" value="{{ now()->format('Y-m-d\TH:i') }}" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-success" id="btnEnregistrerPaiementColis" onclick="enregistrerPaiementColis();">Enregistrer</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalHistoriquePaiementColis" tabindex="-1" aria-labelledby="modalHistoriquePaiementColisLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalHistoriquePaiementColisLabel">Historique des paiements du colis</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <div id="historiquePaiementColisChargement" class="text-center py-3 d-none">Chargement des paiements...</div>
        <div id="historiquePaiementColisMessage" class="alert d-none" role="status"></div>
        <div id="historiquePaiementColisContenu"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<script>
  function formaterMontantPaiement(montant) {
    return Number(montant || 0).toLocaleString('fr-FR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }) + ' CFA';
  }

  function dateLocalePaiement() {
    const date = new Date();
    const pad = valeur => String(valeur).padStart(2, '0');
    return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) +
      'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
  }

  function ouvrirModalPaiementColis(bouton) {
    const form = document.getElementById('formPaiementColis');
    const total = Number(bouton.dataset.montantcolis || 0);
    const paye = Number(bouton.dataset.totalpaiements || 0);
    form.reset();
    form.action = bouton.dataset.enregistrementUrl;
    document.getElementById('paiement_idcolis').value = bouton.dataset.idcolis;
    document.getElementById('paiement_date').value = dateLocalePaiement();
    document.getElementById('paiementColisDetails').textContent =
      'Colis ' + bouton.dataset.codecolis + ' — Montant : ' + formaterMontantPaiement(total) +
      ' — Déjà payé : ' + formaterMontantPaiement(paye) +
      ' — Solde : ' + formaterMontantPaiement(Math.max(0, total - paye));
    const alertBox = document.getElementById('alertPaiementColis');
    alertBox.className = 'alert d-none';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPaiementColis')).show();
  }

  function ouvrirHistoriquePaiementColis(bouton) {
    const modal = document.getElementById('modalHistoriquePaiementColis');
    const chargement = document.getElementById('historiquePaiementColisChargement');
    const message = document.getElementById('historiquePaiementColisMessage');
    const contenu = document.getElementById('historiquePaiementColisContenu');
    chargement.classList.remove('d-none');
    message.className = 'alert d-none';
    contenu.replaceChildren();
    document.getElementById('modalHistoriquePaiementColisLabel').textContent =
      'Historique des paiements — Colis ' + bouton.dataset.codecolis;
    bootstrap.Modal.getOrCreateInstance(modal).show();

    fetch(bouton.dataset.paiementsUrl, {
      headers: { 'Accept': 'application/json' }
    })
      .then(function (response) {
        return response.json().then(function (data) {
          if (!response.ok) throw new Error(data.message || 'Impossible de charger les paiements.');
          return data;
        });
      })
      .then(function (data) {
        if (!data.paiements.length) {
          message.textContent = 'Aucun paiement enregistré pour ce colis.';
          message.className = 'alert alert-info';
          return;
        }

        const table = document.createElement('table');
        table.className = 'table table-bordered table-striped align-middle mb-0';
        const entete = table.createTHead().insertRow();
        ['Date du paiement', 'Montant (CFA)'].forEach(function (libelle) {
          const cellule = document.createElement('th');
          cellule.scope = 'col';
          cellule.textContent = libelle;
          entete.appendChild(cellule);
        });
        const corps = table.createTBody();
        data.paiements.forEach(function (paiement) {
          const ligne = corps.insertRow();
          ligne.insertCell().textContent = paiement.payment_date || '';
          ligne.insertCell().textContent = formaterMontantPaiement(paiement.montant);
        });
        const total = corps.insertRow();
        total.insertCell().textContent = 'Total payé';
        total.insertCell().textContent = formaterMontantPaiement(data.total);
        contenu.appendChild(table);
      })
      .catch(function (erreur) {
        message.textContent = erreur.message;
        message.className = 'alert alert-danger';
      })
      .finally(function () {
        chargement.classList.add('d-none');
      });
  }

  function enregistrerPaiementColis() {
    const form = document.getElementById('formPaiementColis');
    const bouton = document.getElementById('btnEnregistrerPaiementColis');
    const alertBox = document.getElementById('alertPaiementColis');
    if (!form.reportValidity()) return;

    bouton.disabled = true;
    bouton.textContent = 'Enregistrement...';
    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      },
      body: new FormData(form)
    })
      .then(function (response) {
        return response.json().then(function (data) {
          if (!response.ok || !data.success) {
            const erreurs = data.errors ? Object.values(data.errors).flat().join(' ') : '';
            throw new Error(erreurs || data.message || 'Erreur lors de l’enregistrement du paiement.');
          }
          return data;
        });
      })
      .then(function (data) {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPaiementColis')).hide();
        alert(data.message);
        window.location.reload();
      })
      .catch(function (erreur) {
        alertBox.textContent = erreur.message;
        alertBox.className = 'alert alert-danger';
      })
      .finally(function () {
        bouton.disabled = false;
        bouton.textContent = 'Enregistrer';
      });
  }
</script>

<div class="modal fade" id="modalHistoriqueExpedition" tabindex="-1" aria-labelledby="modalHistoriqueExpeditionLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalHistoriqueExpeditionLabel">Historique du colis</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <div id="historiqueExpeditionChargement" class="text-center py-3 d-none">Chargement de l'historique...</div>
        <div id="historiqueExpeditionMessage" class="alert d-none" role="status"></div>
        <div id="historiqueExpeditionContenu"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
<script>
  function ouvrirHistoriqueExpedition(id) {
    const modalElement = document.getElementById('modalHistoriqueExpedition');
    const chargement = document.getElementById('historiqueExpeditionChargement');
    const message = document.getElementById('historiqueExpeditionMessage');
    const contenu = document.getElementById('historiqueExpeditionContenu');
    chargement.classList.remove('d-none');
    message.className = 'alert d-none';
    contenu.replaceChildren();
    bootstrap.Modal.getOrCreateInstance(modalElement).show();

    fetch('/expedition/' + encodeURIComponent(id) + '/historique', {
      headers: { 'Accept': 'application/json' }
    })
      .then(function (response) {
        if (!response.ok) throw new Error('Impossible de charger l’historique du colis.');
        return response.json();
      })
      .then(function (donnees) {
        const titre = document.createElement('h6');
        titre.className = 'mb-3';
        titre.textContent = 'Colis ' + donnees.expedition.code;
        contenu.appendChild(titre);

        if (!donnees.historique.length) {
          message.textContent = 'Aucune étape enregistrée pour ce colis.';
          message.className = 'alert alert-info';
          return;
        }

        const table = document.createElement('table');
        table.className = 'table table-bordered table-striped align-middle mb-0';
        const entete = table.createTHead().insertRow();
        ['Date', 'Origine', 'Étape', 'Référence'].forEach(function (libelle) {
          const cellule = document.createElement('th');
          cellule.scope = 'col';
          cellule.textContent = libelle;
          entete.appendChild(cellule);
        });
        const corps = table.createTBody();
        donnees.historique.forEach(function (etape) {
          const ligne = corps.insertRow();
          [etape.datetat, etape.origine, etape.etat, etape.reference].forEach(function (valeur) {
            ligne.insertCell().textContent = valeur || '';
          });
        });
        contenu.appendChild(table);
      })
      .catch(function (erreur) {
        message.textContent = erreur.message;
        message.className = 'alert alert-danger';
      })
      .finally(function () {
        chargement.classList.add('d-none');
      });
  }
</script>
@endif

<!-- Modale pour afficher les horaires d'un groupe -->

<div class="modal fade" id="modalRtrait" tabindex="-1" aria-labelledby="modalRtraitLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalRtraitLabel">Retrait de transfert</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">

        <!-- <div id="alertModalActeur" class="alert alert-danger d-none mb-3"></div>
                <form id="formModalActeur" enctype="multipart/form-data">

                </form> -->
        <div id="loadingInfos" class="text-center my-3" style="display: none;">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
        </div>
        <div id="contentInfos">
          <!-- Le contenu des informations sera chargé ici via AJAX -->
        </div>
      </div>

    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
    </div>
  </div>
</div>
</div>

<!-- Modale pour afficher les activités d'un projet/programme -->
<div class="modal fade" id="modalActivites" tabindex="-1" aria-labelledby="modalActivitesLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalActivitesLabel">Activités du Projet/Programme</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="loadingActivites" class="text-center my-3" style="display: none;">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
        </div>
        <div id="contentActivites">
          <!-- Le contenu des activités sera chargé ici via AJAX -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>


<!-- Modale pour afficher les responsabilisation des activités d'un projet/programme -->
<div class="modal fade" id="modalAssocieactivites" tabindex="-1" aria-labelledby="modalAssocieactivitesLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAssocieactivitesLabel">Attribution des rôles aux groupes sur l'activité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="loadingAssocieactivites" class="text-center my-3" style="display: none;">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
        </div>
        <div id="contentAssocieactivites">
          <!-- Le contenu des activités sera chargé ici via AJAX -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalHistoexpedition" tabindex="-1" aria-labelledby="modalHistoexpeditionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalHistoexpeditionLabel">Enregistrer une étape</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <div id="alertHistoexpedition" class="alert d-none"></div>
        <form id="formHistoexpedition">
          @csrf
          <input type="hidden" id="histo_idexpedition" name="idexpedition">
          <input type="hidden" id="histo_action" name="action">
          <div id="histo_expedition_details" class="border rounded p-3 mb-3 d-none"></div>
          <div class="mb-3">
            <label for="histo_datetat" class="form-label">Date de l'étape (*)</label>
            <input type="date" class="form-control" id="histo_datetat" name="datetat" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="mb-3">
            <label for="histo_reference" class="form-label">Référence</label>
            <input type="text" class="form-control" id="histo_reference" name="reference" maxlength="255">
          </div>
          <div class="mb-3 d-none" id="histo_montantpaye_container">
            <label for="histo_montantpaye" class="form-label">Montant payé (CFA) (*)</label>
            <input type="number" class="form-control" id="histo_montantpaye" name="montantpaye" min="0" step="0.01">
          </div>
          <div class="mb-3 d-none" id="histo_conteneur_container">
            <label for="histo_idconteneur" class="form-label">Conteneur (*)</label>
            @php
              echo chargerCombo($conteneurs, 'id', 'numconteneur', 'idconteneur', 'Choisir le conteneur');
            @endphp
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary" id="btnEnregistrerHistoexpedition" onclick="enregistrerHistoexpedition();">Enregistrer</button>
      </div>
    </div>
  </div>
</div>

<script>
  const libellesHistoexpedition = {
    retraitcolis: 'Retrait colis',
    envoicolis: 'Envoi colis',
    passagedouane: 'Passage douane',
    arrivemagasin: 'Arrivée magasin',
    retraitmagasin: 'Retrait magasin'
  };

  function ouvrirModalHistoexpedition(id, action, bouton) {
    const form = document.getElementById('formHistoexpedition');
    const conteneurContainer = document.getElementById('histo_conteneur_container');
    const conteneurSelect = document.getElementById('idconteneur');
    const montantPayeContainer = document.getElementById('histo_montantpaye_container');
    const montantPayeInput = document.getElementById('histo_montantpaye');
    form.reset();
    document.getElementById('histo_idexpedition').value = id;
    document.getElementById('histo_action').value = action;
    document.getElementById('histo_datetat').value = new Date().toISOString().slice(0, 10);
    const envoiColis = action === 'envoicolis';
    const retraitColis = action === 'retraitcolis';
    const detailsContainer = document.getElementById('histo_expedition_details');
    detailsContainer.replaceChildren();
    detailsContainer.classList.toggle('d-none', !retraitColis);
    if (retraitColis && bouton?.dataset.expedition) {
      const expedition = JSON.parse(bouton.dataset.expedition);
      montantPayeInput.value = Math.max(
        0,
        Number(expedition.montant || 0) - Number(expedition.totalpaiementcolis || 0)
      ).toFixed(2);
      const titre = document.createElement('strong');
      titre.textContent = 'Expédition ' + (expedition.code || '');
      detailsContainer.appendChild(titre);
      const details = document.createElement('dl');
      details.className = 'row mb-0 mt-2';
      [
        ['Date', expedition.datenreg],
        ['Expéditeur', expedition.expediteur],
        ['Récepteur', expedition.recepteur],
        ['Trajet', expedition.trajet],
        ['Type de colis', expedition.typecolis],
        ['Conteneur', expedition.conteneur],
        ['Nombre de colis', expedition.nbrcolis],
        ['Poids', expedition.poids],
        ['Montant de l’expédition', expedition.montant],
        ['Description / repère', expedition.marque]
      ].forEach(function ([libelle, valeur]) {
        const terme = document.createElement('dt');
        terme.className = 'col-5';
        terme.textContent = libelle;
        const definition = document.createElement('dd');
        definition.className = 'col-7';
        definition.textContent = valeur ?? '';
        details.append(terme, definition);
      });
      detailsContainer.appendChild(details);
    }
    conteneurContainer.classList.toggle('d-none', !envoiColis);
    conteneurSelect.required = envoiColis;
    montantPayeContainer.classList.toggle('d-none', !retraitColis);
    montantPayeInput.required = retraitColis;
    document.getElementById('modalHistoexpeditionLabel').textContent = 'Enregistrer : ' + libellesHistoexpedition[action];
    document.getElementById('alertHistoexpedition').className = 'alert d-none';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalHistoexpedition')).show();
  }

  function enregistrerHistoexpedition() {
    const form = document.getElementById('formHistoexpedition');
    const button = document.getElementById('btnEnregistrerHistoexpedition');
    const alertBox = document.getElementById('alertHistoexpedition');
    if (!form.reportValidity()) return;

    button.disabled = true;
    button.textContent = 'Enregistrement...';
    const afficherRecu = document.getElementById('histo_action').value === 'retraitcolis';
    const fenetreRecu = afficherRecu ? window.open('', '_blank') : null;
    fetch('/enregistrer-histoexpedition', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        },
        body: new FormData(form)
      })
      .then(response => response.json().then(data => ({
        ok: response.ok,
        data
      })))
      .then(({
        ok,
        data
      }) => {
        if (!ok || !data.success) throw new Error(data.message || 'Erreur lors de l\'enregistrement.');
        if (data.receipt_url) {
          if (fenetreRecu) fenetreRecu.location.href = data.receipt_url;
          else {
            window.location.href = data.receipt_url;
            return;
          }
        }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalHistoexpedition')).hide();
        alert(data.message);
        window.location.reload();
      })
      .catch(error => {
        if (fenetreRecu) fenetreRecu.close();
        alertBox.textContent = error.message;
        alertBox.className = 'alert alert-danger';
      })
      .finally(() => {
        button.disabled = false;
        button.textContent = 'Enregistrer';
      });
  }
</script>

<div class="modal fade" id="modalHistoconteneur" tabindex="-1" aria-labelledby="modalHistoconteneurLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalHistoconteneurLabel">Enregistrer une étape</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <div id="alertHistoconteneur" class="alert d-none"></div>
        <form id="formHistoconteneur">
          @csrf
          <input type="hidden" id="histo_ideconteneur" name="ideconteneur">
          <input type="hidden" id="histo_action_conteneur" name="action">
          <div class="mb-3">
            <label for="histo_datetat_conteneur" class="form-label">Date de l'étape (*)</label>
            <input type="date" class="form-control" id="histo_datetat_conteneur" name="datetat" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="mb-3">
            <div id="histo_douane_conteneur_container" class="d-none">
              <label for="histo_douane_conteneur" class="form-label">Montant douane (CFA) (*)</label>
              <input type="number" class="form-control" id="histo_douane_conteneur" name="douane" min="0" step="0.01">
            </div>
          </div>
          <div class="mb-3">
            <label for="histo_observation_conteneur" class="form-label">Observation</label>
            <input type="text" class="form-control" id="histo_observation_conteneur" name="observation" maxlength="255">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary" id="btnEnregistrerHistoconteneur" onclick="enregistrerHistoconteneur();">Enregistrer</button>
      </div>
    </div>
  </div>
</div>

<script>
  const libellesHistoconteneur = {
    envoyer: 'Envoyer',
    passagedouane: 'Passage douane',
    arriveemagasin: 'Arrivée au magasin'
  };

  function ouvrirModalHistoconteneur(id, action) {
    const form = document.getElementById('formHistoconteneur');
    form.reset();
    document.getElementById('histo_ideconteneur').value = id;
    document.getElementById('histo_action_conteneur').value = action;
    document.getElementById('histo_datetat_conteneur').value = new Date().toISOString().slice(0, 10);
    const douaneContainer = document.getElementById('histo_douane_conteneur_container');
    const douaneInput = document.getElementById('histo_douane_conteneur');
    const passageDouane = action === 'passagedouane';
    douaneContainer.classList.toggle('d-none', !passageDouane);
    douaneInput.required = passageDouane;
    document.getElementById('modalHistoconteneurLabel').textContent = 'Enregistrer : ' + libellesHistoconteneur[action];
    document.getElementById('alertHistoconteneur').className = 'alert d-none';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalHistoconteneur')).show();
  }

  function enregistrerHistoconteneur() {
    const form = document.getElementById('formHistoconteneur');
    const button = document.getElementById('btnEnregistrerHistoconteneur');
    const alertBox = document.getElementById('alertHistoconteneur');
    if (!form.reportValidity()) return;

    button.disabled = true;
    button.textContent = 'Enregistrement...';
    fetch('/enregistrer-histoconteneur', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        },
        body: new FormData(form)
      })
      .then(response => response.json().then(data => ({ ok: response.ok, data })))
      .then(({ ok, data }) => {
        if (!ok || !data.success) throw new Error(data.message || 'Erreur lors de l\'enregistrement.');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalHistoconteneur')).hide();
        alert(data.message);
        window.location.reload();
      })
      .catch(error => {
        alertBox.textContent = error.message;
        alertBox.className = 'alert alert-danger';
      })
      .finally(() => {
        button.disabled = false;
        button.textContent = 'Enregistrer';
      });
  }
</script>
@endsection