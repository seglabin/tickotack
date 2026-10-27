<div class="row">
    {{-- `code`, `datetransfert`, `montdepot`, `montretrait`, `taux`, `description`, `observation`, `datepayement`, `idexpediteur`, `idrecepteur`, `idmoyentransfert`, `iddepart`, `iddestination`, `idetat`, `idcaisse` --}}
    @php
    $codeDefaut = ($a != null) ? $a->code : 'TRF-' . strtoupper(Str::random(6));
    $dateDefaut = ($a != null) ? $a->datetransfert : date('Y-m-d\TH:i');
    $tauxDefaut = ($a != null) ? $a->taux : '1';

    $u = Auth::user();
    $idcaisse = ($a != null) ? $a->idcaisse : (($u != null) ? $u->idcaisse : '');
    $disabled = ($u != null && $u->idrole == 1) ? '' : 'disabled';
    $caissesAdresses = $caisses->pluck('idadresse', 'id')->toArray();
    $iddepart = $caissesAdresses[$idcaisse] ?? (($a != null) ? $a->iddepart : '');

    @endphp

    <!-- Code & Date -->
    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="code" class="lelabel">Code transfert (*)</label>
                <input type="text" class="form-control" id="code" name="code"
                    onkeyup="enMajuscule('code');"
                    placeholder="Ex : TRF-ABC123"
                    value="{{ $codeDefaut }}" readonly>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="datetransfert" class="lelabel">Date du transfert (*)</label>
                <input type="datetime-local" class="form-control" id="datetransfert" name="datetransfert"
                    value="{{ $dateDefaut }}">
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="idcaisse" class="lelabel">Caisse (*)</label>
                @php
                echo chargerCombo($caisses, 'id', 'libelle', 'idcaisse', 'Choisir une caisse', '', 'actualiserDepartSelonCaisse()', $idcaisse, $disabled);
                @endphp
                @if($disabled !== '')
                <input type="hidden" name="idcaisse" value="{{ $idcaisse }}">
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="idmoyentransfert" class="lelabel">Moyen de transfert (*)</label>
                @php
                $idmoyentransfert = ($a != null) ? $a->idmoyentransfert : '';
                echo chargerCombo($moyentransferts, 'id', 'libelle', 'idmoyentransfert', 'Choisir un moyen', '', '', $idmoyentransfert);
                @endphp
            </div>
        </div>
    </div>

    <!-- Acteurs (Expéditeur & Récepteur) avec bouton Nouveau -->
    <div class="col-md-6">
        <div class="card-body">
            <div class="form-group">
                <label for="idexpediteur" class="lelabel">Expéditeur (*)</label>
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <div style="flex-grow: 1;">
                        @php
                        $idexpediteur = ($a != null) ? $a->idexpediteur : '';
                        echo chargerCombo($acteurs, 'id', 'nom', 'idexpediteur', 'Choisir l\'expéditeur', '', '', $idexpediteur);
                        @endphp
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="ouvrirModalActeur('idexpediteur')" title="Ajouter un nouvel expéditeur" style="white-space: nowrap; height: 38px; border-radius: 8px;">
                        <i class="fa fa-plus-circle"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-body">
            <div class="form-group">
                <label for="idrecepteur" class="lelabel">Récepteur (*)</label>
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <div style="flex-grow: 1;">
                        @php
                        $idrecepteur = ($a != null) ? $a->idrecepteur : '';
                        echo chargerCombo($acteurs, 'id', 'nom', 'idrecepteur', 'Choisir le récepteur', '', '', $idrecepteur);
                        @endphp
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="ouvrirModalActeur('idrecepteur')" title="Ajouter un nouveau récepteur" style="white-space: nowrap; height: 38px; border-radius: 8px;">
                        <i class="fa fa-plus-circle"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Origine / Destination / Taux -->
    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="iddepart" class="lelabel">Ville de départ (*)</label>
                @php
                echo chargerCombo($adresses, 'id', 'libelle', 'iddepart', 'Choisir le départ', '', 'actualiserTauxAutomatique()', $iddepart, 'disabled');
                @endphp
                <input type="hidden" id="iddepart_hidden" name="iddepart" value="{{ $iddepart }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="iddestination" class="lelabel">Ville de destination (*)</label>
                @php
                $iddestination = ($a != null) ? $a->iddestination : '';
                echo chargerCombo($adresses, 'id', 'libelle', 'iddestination', 'Choisir la destination', '', "controlerTrajetTransfert('iddestination')", $iddestination);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="taux" class="lelabel">Taux de change (*)</label>
                <input type="hidden" id="operateur" name="operateur" value="*">
                <input type="text" class="form-control" id="taux" name="taux"
                    placeholder="Généré automatiquement"
                    value="{{ $tauxDefaut }}" readonly style="background-color: #f8f9fa; cursor: not-allowed; font-weight: bold;">
            </div>
        </div>
    </div>

    <!-- Montants -->
    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="montdepot" class="lelabel">Montant déposé (*)</label>
                <input type="text" class="form-control" id="montdepot" name="montdepot"
                    onkeyup="typemontant(this); calculerMontantRetrait();"
                    placeholder="Ex : 100 000"
                    value="{{ ($a != null) ? $a->montdepot : null }}">
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="montretrait" class="lelabel">Montant à retirer (*)</label>
                <input type="text" class="form-control" id="montretrait" name="montretrait"
                    placeholder="Calculé automatiquement (Dépôt x Taux)"
                    value="{{ ($a != null) ? $a->montretrait : null }}" readonly
                    style="background-color: #f8f9fa; cursor: not-allowed; font-weight: bold;">
            </div>
        </div>
    </div>
    <!-- 
    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="idetat" class="lelabel">État du transfert (*)</label>
                @php
                $idetat = ($a != null) ? $a->idetat : '';
                echo chargerCombo($etats, 'id', 'libelle', 'idetat', 'Choisir l\'état', '', 'gererChampsPaiement()', $idetat);
                @endphp
            </div>
        </div>
    </div> -->

    <!-- Description & Observation -->
    <div class="col-md-6">
        <div class="card-body">
            <div class="form-group">
                <label for="description" class="lelabel">Description (*)</label>
                <textarea class="form-control" id="description" name="description" rows="3"
                    placeholder="Motif ou détails du transfert">{{ ($a != null) ? $a->description : "PAIEMENT FOURNISSEUR" }}</textarea>
            </div>
        </div>
    </div>
</div>

@section('modals')
<!-- Modale d'ajout rapide d'un acteur (Expéditeur / Récepteur) -->
<div class="modal fade" id="modalNouveauActeur" tabindex="-1" aria-labelledby="modalNouveauActeurLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="modalNouveauActeurLabel">
                    <i class="fa fa-user-plus me-2"></i> Nouvel Acteur
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <div id="alertModalActeur" class="alert alert-danger d-none mb-3"></div>
                <form id="formModalActeur" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="modal_nom" class="lelabel">Nom complet (*)</label>
                            <input type="text" class="form-control" id="modal_nom" name="nom" required placeholder="Ex : KOUAME Jean">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="modal_tel" class="lelabel">Téléphone (*)</label>
                            <input type="text" class="form-control" id="modal_tel" name="tel" required onkeyup="typetelephone(this);" placeholder="Ex : 01 02 03 04 05">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="modal_whatsap" class="lelabel">WhatsApp</label>
                            <input type="text" class="form-control" id="modal_whatsap" name="whatsap" onkeyup="typetelephone(this);" placeholder="Ex : 01 02 03 04 05">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="modal_carte" class="lelabel">Photo de la carte</label>
                            <div class="control-group">
                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                    <div class="fileupload-preview thumbnail" style="width: 120px; height: 120px;"></div>
                                    <div>
                                        <span class="btn btn-file btn-small btn-success" title="Choisir">
                                            <span class="fileupload-new"><i class="fa fa-camera"></i></span>
                                            <span class="fileupload-exists btn-small btn-warning" title="Changer"><i class="fa fa-camera"></i></span>
                                            <input type="file" name="carte" id="modal_carte" accept="image/*" />
                                        </span>
                                        <span class="btn btn-small btn-danger" data-dismiss="fileupload" title="Supprimer">
                                            <i class="fa fa-trash"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btnarrondi" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary btnarrondi" id="btnEnregistrerActeurModal" onclick="soumettreNouvelActeur();">
                    <i class="fa fa-save me-1"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    // Grille des taux de change transmis depuis le contrôleur
    const grilleTaux = @json($tauxGrille ?? []);
    let cibleComboActeur = 'idexpediteur'; // Champ à mettre à jour lors de l'ajout (expéditeur ou récepteur)

    function ouvrirModalActeur(cible) {
        cibleComboActeur = cible;
        const libelleCible = (cible === 'idexpediteur') ? "un nouvel expéditeur" : "un nouveau récepteur";
        $('#modalNouveauActeurLabel').html('<i class="fa fa-user-plus me-2"></i> Ajouter ' + libelleCible);

        // Réinitialiser le formulaire
        $('#formModalActeur')[0].reset();
        $('#alertModalActeur').addClass('d-none').text('');

        // Ouvrir la modal Bootstrap
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalNouveauActeur')).show();
    }

    function soumettreNouvelActeur() {
        const nom = $('#modal_nom').val().trim();
        const tel = $('#modal_tel').val().trim();
        const alertBox = $('#alertModalActeur');

        if (!nom || !tel) {
            alertBox.text("Veuillez renseigner le nom complet et le numéro de téléphone.");
            alertBox.removeClass('d-none');
            return;
        }

        alertBox.addClass('d-none');
        const btn = $('#btnEnregistrerActeurModal');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Enregistrement...');

        const form = document.getElementById('formModalActeur');
        const formData = new FormData(form);

        fetch(@json(url('enregistrer-acteur-ajax')), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json().then(data => ({
                ok: response.ok,
                body: data
            })))
            .then(({
                ok,
                body
            }) => {
                btn.prop('disabled', false);
                btn.html('<i class="fa fa-save me-1"></i> Enregistrer');

                if (ok && body.success) {
                    const nouvelActeur = body.acteur;
                    const optTexte = nouvelActeur.nom + (nouvelActeur.tel ? ' (' + nouvelActeur.tel + ')' : '');

                    // Ajouter l'option dans les deux combos (expéditeur et récepteur)
                    ['idexpediteur', 'idrecepteur'].forEach(function(selectId) {
                        const select = document.getElementById(selectId);
                        if (select) {
                            const newOption = new Option(optTexte, nouvelActeur.id, false, false);
                            select.add(newOption);
                            if (selectId === cibleComboActeur) {
                                select.value = nouvelActeur.id;
                                if (typeof jQuery !== 'undefined' && $(select).hasClass('select2')) {
                                    $(select).trigger('change');
                                }
                            }
                        }
                    });

                    // Fermer la modal
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalNouveauActeur')).hide();
                } else {
                    alertBox.text(body.message || "Erreur lors de l'enregistrement de l'acteur.");
                    alertBox.removeClass('d-none');
                }
            })
            .catch(error => {
                console.error('Erreur lors de la création de l’acteur :', error);
                btn.prop('disabled', false);
                btn.html('<i class="fa fa-save me-1"></i> Enregistrer');
                alertBox.text("Une erreur est survenue lors de la communication avec le serveur.");
                alertBox.removeClass('d-none');
            });
    }

    function actualiserTauxAutomatique() {
        const depEl = document.getElementById('iddepart');
        const destEl = document.getElementById('iddestination');
        const champTaux = document.getElementById('taux');
        const champOperateur = document.getElementById('operateur');

        const dep = depEl ? depEl.value : '0';
        const dest = destEl ? destEl.value : '0';

        if (dep && dest && dep !== '0' && dest !== '0') {
            const cle = dep + '_' + dest;
            if (grilleTaux.hasOwnProperty(cle)) {
                champTaux.value = grilleTaux[cle].split('|')[0];
                champOperateur.value = grilleTaux[cle].split('|')[1];
                calculerMontantRetrait();
            } else {
                champTaux.value = '';
                const champRetrait = document.getElementById('montretrait');
                if (champRetrait) champRetrait.value = '';
                alert("Aucun taux de change n'est défini pour ce trajet (" + (depEl.options[depEl.selectedIndex]?.text || '') + " -> " + (destEl.options[destEl.selectedIndex]?.text || '') + "). Veuillez le configurer dans les paramètres.");
            }
        }
    }

    function controlerTrajetTransfert(champModifie) {
        const departEl = document.getElementById('iddepart');
        const destinationEl = document.getElementById('iddestination');
        const departHiddenEl = document.getElementById('iddepart_hidden');

        if (!departEl || !destinationEl) return;

        if (departEl.value !== '0' && destinationEl.value !== '0' && departEl.value === destinationEl.value) {
            alert('La ville de départ doit être différente de la ville de destination.');

            const champ = document.getElementById(champModifie);
            if (champModifie === 'iddepart') {
                destinationEl.value = '0';
                $(destinationEl).trigger('change');
            } else {
                champ.value = '0';
                $(champ).trigger('change');
            }
        }

        if (departHiddenEl) departHiddenEl.value = departEl.value;
        actualiserTauxAutomatique();
    }

    function actualiserDepartSelonCaisse() {
        const caisseEl = document.getElementById('idcaisse');
        const departEl = document.getElementById('iddepart');
        const departHiddenEl = document.getElementById('iddepart_hidden');
        const caisses = @json($caissesAdresses);

        if (!caisseEl || !departEl || !departHiddenEl) return;

        const idAdresse = caisses[caisseEl.value] || '0';
        departEl.value = idAdresse;
        departHiddenEl.value = idAdresse;
        $(departEl).trigger('change');
        controlerTrajetTransfert('iddepart');
    }

    function calculerMontantRetrait() {
        const champRetrait = document.getElementById('montretrait');
        const montDepotStr = document.getElementById('montdepot') ? document.getElementById('montdepot').value.replace(/\s/g, '') : '';
        const tauxStr = document.getElementById('taux') ? document.getElementById('taux').value.replace(',', '.').replace(/\s/g, '') : '';
        const operateurStr = document.getElementById('operateur') ? document.getElementById('operateur').value : '';
        const montDepot = parseFloat(montDepotStr);
        const taux = parseFloat(tauxStr);
        // alert(`Calcul du montant à retirer : Montant Déposé = ${montDepot}, Taux = ${taux}, Opérateur = ${operateurStr}`);
        if (!isNaN(montDepot) && !isNaN(taux) && taux > 0 && montDepot > 0) {
            // const montRetrait = montDepot operateurStr taux;
            let montRetrait;
            switch (operateurStr) {
                case '*':
                    montRetrait = montDepot * taux;
                    break;
                case '/':
                    montRetrait = montDepot / taux;
                    break;
                case '+':
                    montRetrait = montDepot + taux;
                    break;
                case '-':
                    montRetrait = montDepot - taux;
                    break;
            }
            if (champRetrait) {
                champRetrait.value = formatmontant(Math.round(montRetrait));

            } else if (champRetrait) {
                champRetrait.value = '';
            }
        }
    }


    function gererChampsPaiement() {
        // Optionnel : adapter l'affichage selon l'état
    }

    // Liaison automatique avec jQuery / Select2 si présent
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function($) {
            $('#iddepart, #iddestination').on('change select2:select', function() {
                controlerTrajetTransfert(this.id);
            });
            $('#idcaisse').on('change select2:select', function() {
                actualiserDepartSelonCaisse();
            });
            actualiserDepartSelonCaisse();
        });
    }
</script>