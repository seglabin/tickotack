
@php 
  //$caissesAdresses = $caisses->pluck('idadresse', 'id')->toArray();
//$iddepart = $caissesAdresses[$idcaisse] ?? (($a != null) ? $a->iddepart : '');

@endphp
<div class="row" id="conteneurForm" data-conteneur-id="{{ $id ?: '' }}" data-normal-type-id="{{ $normalTypeId }}" data-expeditions-url="{{ route('conteneur.expeditions-disponibles') }}">
    <input type="hidden" name="expedition_ids_present" value="1">
    @php
        $dateDepart = $a?->datedepart
            ? \Illuminate\Support\Carbon::parse($a->datedepart)->format('Y-m-d\\TH:i')
            : (old('datedepart')??date('Y-m-d\TH:i'));
            
    @endphp
 <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="idmoyentransport" class="lelabel">Moyen de transport (*)</label>
                @php
                    $idmoyentransport = request('idmoyentransport', old('idmoyentransport', $a?->idmoyentransport));
                    echo chargerCombo($moyentransports, 'id', 'libelle', 'idmoyentransport', 'Choisir un moyen de transport', '', '', $idmoyentransport);
                @endphp
            </div>
        </div>
    </div>
    
    <div class="col-md-3" id="zoneTypeEnvoi">
        <div class="card-body">
            <div class="form-group">
                <label for="idtypenvoi" class="lelabel">Type d'envoi (*)</label>
                @php
                    $idtypenvoi = request('idtypenvoi', old('idtypenvoi', $a?->idtypenvoi));
                    echo chargerCombo($typenvois, 'id', 'libelle', 'idtypenvoi', 'Choisir un type d\'envoi', '', '', $idtypenvoi);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="numconteneur" class="lelabel">Référence (*)</label>
                <input type="text" class="form-control" id="numconteneur" name="numconteneur" onkeyup="this.value = this.value.toUpperCase();"
                    value="{{ request('numconteneur', old('numconteneur', $a?->numconteneur)) }}" maxlength="100" required>
            </div>
        </div>
    </div>

    <div class="col-md-3 zoneBateau">
        <div class="card-body">
            <div class="form-group">
                <label for="typeconteneur" class="lelabel">Type de conteneur (*)</label>
                <input type="text" class="form-control" id="typeconteneur" name="typeconteneur" onkeyup="this.value = this.value.toUpperCase();"
                    value="{{ request('typeconteneur', old('typeconteneur', $a?->typeconteneur)) }}" maxlength="30" required>
            </div>
        </div>
    </div>

    <div class="col-md-3 zoneBateau">
        <div class="card-body">
            <div class="form-group">
                <label for="idconsignataire" class="lelabel">Consignataire (*)</label>
                @php
                    $idconsignataire = request('idconsignataire', old('idconsignataire', $a?->idconsignataire));
                    echo chargerCombo($consignataires, 'id', 'libelle', 'idconsignataire', 'Choisir un consignataire', '', '', $idconsignataire);
                @endphp
            </div>
        </div>
    </div>

   <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="iddepart" class="lelabel">Ville de départ (*)</label>
                @php
                $iddepart = request('iddepart', old('iddepart', $a?->iddepart));
                echo chargerCombo($adresses, 'id', 'libelle', 'iddepart', 'Choisir le départ', '', '', $iddepart);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="iddestination" class="lelabel">Ville de destination (*)</label>
                @php
                $iddestination = request('iddestination', old('iddestination', $a?->iddestination));
                echo chargerCombo($adresses, 'id', 'libelle', 'iddestination', 'Choisir la destination', '', '', $iddestination);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="datedepart" class="lelabel">Date de départ</label>
                <input type="datetime-local" class="form-control" id="datedepart" name="datedepart"
                    value="{{ request('datedepart', $dateDepart) }}">
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="poidstotal" class="lelabel">Poids total</label>
                <input type="number" class="form-control" id="poidstotal" name="poidstotal"
                    value="{{ request('poidstotal', old('poidstotal', $a?->poidstotal)) }}" min="0" step="0.01" readonly>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 zoneBateau">
        <div class="card-body">
            <div class="form-group">
                <label for="coutcfa" class="lelabel">Coût (CFA) (*)</label>
                <input type="number" class="form-control" id="coutcfa" name="coutcfa"
                    value="{{ request('coutcfa', old('coutcfa', $a?->coutcfa)) }}" min="0" step="0.01" required>
            </div>
        </div>
    </div>

    <!-- <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="douane" class="lelabel">Douane</label>
                <input type="number" class="form-control" id="douane" name="douane"
                    value="{{ request('douane', old('douane', $a?->douane)) }}" min="0" step="0.01">
            </div>
        </div>
    </div> -->

    <div class="col-md-12 zoneBateau">
        <div class="card-body">
            <div class="form-group">
                <label for="lienconteneur" class="lelabel">Lien du conteneur (*)</label>
                <input type="text" class="form-control" id="lienconteneur" name="lienconteneur"
                    value="{{ request('lienconteneur', old('lienconteneur', $a?->lienconteneur)) }}" maxlength="255" required>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card-body">
            <h5>Expéditions à rattacher au conteneur</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Date</th>
                            <th>Expéditeur</th>
                            <th>Récepteur</th>
                            <th>Trajet</th>
                            <th>État</th>
                            <th>Choix</th>
                        </tr>
                    </thead>
                    <tbody id="expeditionsConteneurListe">
                        @forelse($expeditions as $expedition)
                            @php
                                $selectionDemandee = request()->has('expedition_ids_present') || old('expedition_ids') !== null;
                                $expeditionIdsSelectionnees = request()->input('expedition_ids', old('expedition_ids', []));
                                $selectionnee = $selectionDemandee
                                    ? in_array((string) $expedition->id, array_map('strval', (array) $expeditionIdsSelectionnees), true)
                                    : $expedition->idconteneur == ($id ?: 0);
                            @endphp
                            <tr>
                                <td>{{ $expedition->code }}</td>
                                <td>{{ $expedition->datenreg }}</td>
                                <td>{{ $expedition->expediteur }}</td>
                                <td>{{ $expedition->recepteur }}</td>
                                <td>{{ $expedition->villedepart }} - {{ $expedition->villedestination }}</td>
                                <td>{{ $expedition->etat }}</td>
                                <td class="text-center">
                                    <input type="checkbox" name="expedition_ids[]" value="{{ $expedition->id }}" data-poids="{{ $expedition->poids }}" @checked($selectionnee)>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucune expédition disponible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
(() => {
    const conteneurForm = document.getElementById('conteneurForm');
    const selectTransport = document.getElementById('idmoyentransport');
    const selectTypeEnvoi = document.getElementById('idtypenvoi');
    const selectiddestination = document.getElementById('iddestination');
    const selectiddepart = document.getElementById('iddepart');
    const zoneTypeEnvoi = document.getElementById('zoneTypeEnvoi');
    const champPoidsTotal = document.getElementById('poidstotal');
    const listeExpeditions = document.getElementById('expeditionsConteneurListe');
    const selections = new Set();
    const poidsExpeditions = new Map();
    const normalTypeId = conteneurForm.dataset.normalTypeId;
    let requeteExpeditionsActive = null;

    function normaliser(valeur) {
        return valeur.normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim().toLowerCase();
    }

    function estBateau() {
        const option = selectTransport.options[selectTransport.selectedIndex];
        return option && normaliser(option.text).includes('bateau');
    }

    function actualiserChamps() {
        const bateau = estBateau();
        const avion = selectTransport.options[selectTransport.selectedIndex]
            && normaliser(selectTransport.options[selectTransport.selectedIndex].text).includes('avion');

        zoneTypeEnvoi.style.display = bateau ? 'none' : '';
        document.querySelectorAll('.zoneBateau').forEach(function (zone) {
            zone.style.display = avion ? 'none' : '';
        });

        if (bateau && normalTypeId) {
            selectTypeEnvoi.value = normalTypeId;
        }
    }

    function actualiserPoidsTotal() {
        const total = Array.from(selections).reduce(function (somme, id) {
            return somme + (poidsExpeditions.get(id) || 0);
        }, 0);
        champPoidsTotal.value = total.toFixed(2);
    }

    function afficherExpeditions(expeditions) {
        const idsDisponibles = new Set(expeditions.map(function (expedition) { return String(expedition.id); }));
        selections.forEach(function (id) {
            if (!idsDisponibles.has(id)) selections.delete(id);
        });
        listeExpeditions.replaceChildren();
        if (!expeditions.length) {
            const ligne = document.createElement('tr');
            const cellule = document.createElement('td');
            cellule.colSpan = 7;
            cellule.className = 'text-center';
            cellule.textContent = 'Aucune expédition disponible.';
            ligne.appendChild(cellule);
            listeExpeditions.appendChild(ligne);
            return;
        }

        expeditions.forEach(function (expedition) {
            const ligne = document.createElement('tr');
            [expedition.code, expedition.datenreg, expedition.expediteur, expedition.recepteur,
                (expedition.villedepart || '') + ' - ' + (expedition.villedestination || ''), expedition.etat]
                .forEach(function (valeur) {
                    const cellule = document.createElement('td');
                    cellule.textContent = valeur || '';
                    ligne.appendChild(cellule);
                });
            const celluleChoix = document.createElement('td');
            celluleChoix.className = 'text-center';
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'expedition_ids[]';
            checkbox.value = expedition.id;
            checkbox.dataset.poids = expedition.poids || 0;
            checkbox.checked = selections.has(String(expedition.id));
            celluleChoix.appendChild(checkbox);
            ligne.appendChild(celluleChoix);
            listeExpeditions.appendChild(ligne);
            poidsExpeditions.set(String(expedition.id), Number(expedition.poids) || 0);
        });
    }

    async function actualiserExpeditions() {
        actualiserChamps();
        if (requeteExpeditionsActive) {
            requeteExpeditionsActive.abort();
        }
        const requeteExpeditions = new AbortController();
        requeteExpeditionsActive = requeteExpeditions;
        const parametres = new URLSearchParams({
            idenreg: conteneurForm.dataset.conteneurId,
            idmoyentransport: selectTransport.value,
            idtypenvoi: selectTypeEnvoi.value,
            iddestination: selectiddestination.value,
            iddepart: selectiddepart.value
        });
        listeExpeditions.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(conteneurForm.dataset.expeditionsUrl + '?' + parametres.toString(), {
                headers: { 'Accept': 'application/json' },
                signal: requeteExpeditions.signal
            });
            if (!response.ok) throw new Error('Impossible de charger les expéditions.');
            const expeditions = await response.json();
            if (requeteExpeditionsActive === requeteExpeditions) {
                afficherExpeditions(expeditions);
            }
        } catch (error) {
            if (error.name === 'AbortError') return;
            const ligne = document.createElement('tr');
            const cellule = document.createElement('td');
            cellule.colSpan = 7;
            cellule.className = 'text-center text-danger';
            cellule.textContent = error.message;
            ligne.appendChild(cellule);
            listeExpeditions.replaceChildren(ligne);
        } finally {
            if (requeteExpeditionsActive === requeteExpeditions) {
                listeExpeditions.removeAttribute('aria-busy');
                actualiserPoidsTotal();
            }
        }
    }

    function controlerChangementTrajet(event) {
        const selectModifie = window.jQuery(event.currentTarget);
        if (selectiddepart.value !== '0'
            && selectiddestination.value !== '0'
            && selectiddepart.value === selectiddestination.value) {
            alert('La ville de départ doit être différente de la ville de destination.');
            selectModifie.val('0').trigger('change');
            return;
        }

        actualiserExpeditions();
    }

    function lierEvenementsFiltres() {
        if (!window.jQuery) {
            throw new Error('jQuery est nécessaire pour actualiser les expéditions.');
        }

        window.jQuery([selectTransport, selectTypeEnvoi]).on('change', actualiserExpeditions);
        window.jQuery([selectiddepart, selectiddestination]).on('change', controlerChangementTrajet);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', lierEvenementsFiltres, { once: true });
    } else {
        lierEvenementsFiltres();
    }

    listeExpeditions.addEventListener('change', function (event) {
        const checkbox = event.target.closest('input[name="expedition_ids[]"]');
        if (!checkbox) return;
        const id = String(checkbox.value);
        poidsExpeditions.set(id, Number(checkbox.dataset.poids) || 0);
        if (checkbox.checked) selections.add(id);
        else selections.delete(id);
        actualiserPoidsTotal();
    });

    listeExpeditions.querySelectorAll('input[name="expedition_ids[]"]').forEach(function (checkbox) {
        poidsExpeditions.set(String(checkbox.value), Number(checkbox.dataset.poids) || 0);
        if (checkbox.checked) selections.add(String(checkbox.value));
    });
    actualiserChamps();
    actualiserPoidsTotal();
})();
</script>
