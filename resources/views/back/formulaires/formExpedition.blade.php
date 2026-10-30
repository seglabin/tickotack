<div class="row">
    {{-- `datenreg`, `montant`, `code`, `marque`, `iddemarcheur`, `idxpediteur`, `idrecepeur`, `idtypcolis`, `idtypenvoi`, `idmoyentransport`, `iddepart`, `iddestination`, `idetat`, --}}
    @php
    $codeDefaut = ($a != null) ? $a->code : 'EXP-' . strtoupper(Str::random(6));
    $dateDefaut = ($a != null) ? $a->datenreg : date('Y-m-d\TH:i');
    $tauxDefaut = ($a != null) ? $a->taux : '1';
    @endphp

    <!-- Code & Date -->
    <div class="col-md-2">
        <div class="card-body">
            <div class="form-group">
                <label for="code" class="lelabel">Code Coli (*)</label>
                <input type="text" class="form-control" id="code" name="code"
                    onkeyup="enMajuscule('code');"
                    placeholder="Ex : EXP-ABC123"
                    value="{{ $codeDefaut }}" readonly>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="datenreg" class="lelabel">Date d'enregistrement (*)</label>
                <input type="datetime-local" class="form-control" id="datenreg" name="datenreg"
                    value="{{ $dateDefaut }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="idmoyentransport" class="lelabel">Moyen de transport (*)</label>
                @php
                $idmoyentransport = ($a != null) ? $a->idmoyentransport : '';
                echo chargerCombo($moyentransports, 'id', 'libelle', 'idmoyentransport', 'Choisir le moyen de transport', '', 'changeMoyenTransport();', $idmoyentransport);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card-body">
            <div class="form-group">
                <label for="nbrcolis" class="lelabel">Nbre colis (*)</label>
                <input type="text" class="form-control" id="nbrcolis" name="nbrcolis"
                    onkeyup="typemontant(this);"
                    placeholder="Ex : 100 000"
                    value="{{ ($a != null) ? $a->nbrcolis : 1 }}">
            </div>
        </div>
    </div>

    <input type="hidden" id="poids" name="poids" value="{{ ($a != null) ? $a->poids : null }}">

    <div class="col-md-12" id="zonebateau">
        <div class="row">
       
            <div class="col-md-4">
                <div class="card-body">
                    <div class="form-group">
                        <label for="poids" class="lelabel">CBM (*)</label>
                        <div class="d-flex align-items-center" style="gap: 6px;">
                            <div style="flex-grow: 1;">
                                <input type="text" class="form-control" id="poidsB" name="poidsB"
                                    onkeyup="typenumerique(this);" onchange="actualiserTarifAutomatique()"
                                    placeholder="Ex : 100 000"
                                    value="{{ ($a != null) ? $a->poids : null }}">
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" onclick="calculPoids()" title="Calculer le CBM des colis" style="white-space: nowrap; height: 38px; border-radius: 8px;">
                                Calculer CBM
                                <!-- <i class="fa fa-plus-circle"></i> -->
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-12" id="zoneavion">
        <div class="row">
            <div class="col-md-4">
                <div class="card-body">
                    <div class="form-group">
                        <label for="idtypenvoi" class="lelabel">Type d'envoi (*)</label>
                        @php
                        $idtypenvoi = ($a != null) ? $a->idtypenvoi : '';
                        echo chargerCombo($typenvois, 'id', 'libelle', 'idtypenvoi', 'Choisir le type d\'envoi', '', 'actualiserTarifAutomatique()', $idtypenvoi);
                        @endphp
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card-body">
                    <div class="form-group">
                        <label for="poids" class="lelabel">Unité de mesure (*)</label>
                        <input type="text" class="form-control" id="poidsA" name="poidsA"
                            onkeyup="typenumerique(this);" onchange="changePoidesA();"
                            placeholder="Ex : 100 000"
                            value="{{ ($a != null) ? $a->poids : null }}">
                    </div>
                </div>
            </div>

        </div>
    </div>



    {{--
    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="idcaisse" class="lelabel">Caisse d'expédition (*)</label>
                @php
                $idcaisse = ($a != null) ? $a->idcaisse : '';
                echo chargerCombo($caisses, 'id', 'libelle', 'idcaisse', 'Choisir une caisse', '', '', $idcaisse);
                @endphp
            </div>
        </div>
    </div>--}}
    <!-- poids, nbre colis, marque -->


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
                $iddepart = ($a != null) ? $a->iddepart : '';
                echo chargerCombo($adresses, 'id', 'libelle', 'iddepart', 'Choisir le départ', '', "controlerTrajetExpedition('iddepart')", $iddepart);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="iddestination" class="lelabel">Ville de destination (*)</label>
                @php
                $iddestination = ($a != null) ? $a->iddestination : '';
                echo chargerCombo($adresses, 'id', 'libelle', 'iddestination', 'Choisir la destination', '', "controlerTrajetExpedition('iddestination')", $iddestination);
                @endphp
            </div>
        </div>
    </div>
    <!-- `idtypcolis`, `idtypenvoi`, `idmoyentransport` -->
    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="idtypcolis" class="lelabel">Type de colis (*)</label>
                @php
                $idtypcolis = ($a != null) ? $a->idtypcolis : '';
                echo chargerCombo($typcolis, 'id', 'libelle', 'idtypcolis', 'Choisir le type de colis', '', 'actualiserTarifAutomatique()', $idtypcolis);
                @endphp
            </div>
        </div>
    </div>




    <!-- Montants -->
    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="montant" class="lelabel">Montant (*)</label>
                <input type="text" class="form-control" id="montant" name="montant"
                    onkeyup="typemontant(this);"
                    placeholder="Ex : 100 000"
                    value="{{ ($a != null) ? $a->montant : null }}" readonly>
                    </div>
            </div>
        </div>

        <!-- Description & Observation -->
        <div class="col-md-6">
            <div class="card-body">
                <div class="form-group">
                    <label for="marque" class="lelabel">Marque ou description (*)</label>
                    <textarea class="form-control" id="marque" name="marque" rows="3"
                        placeholder="Inscription sur le colis">{{ ($a != null) ? $a->marque : null }}</textarea>
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
                                <label for="modal_whatsap" class="lelabel">WhatsApp (*)</label>
                                <input type="text" class="form-control" id="modal_whatsap" name="whatsap" required onkeyup="typetelephone(this);" placeholder="Ex : 01 02 03 04 05">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="modal_idadreslivr" class="lelabel">Adresse de livraison</label>
                                <select class="form-control select2-modal" id="modal_idadreslivr" name="idadreslivr" style="width: 100%;">
                                    <option value="0">-- Choisir une adresse --</option>
                                    @foreach($adresses as $adr)
                                    <option value="{{ $adr->id }}">{{ $adr->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="modal_marque" class="lelabel">Marque / Repère</label>
                                <textarea class="form-control" id="modal_marque" name="marque" rows="2" placeholder="Description ou repère pour identifier l'acteur"></textarea>
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

    <div class="modal fade" id="modalCalculPoids" tabindex="-1" aria-labelledby="modalCalculPoidsLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalCalculPoidsLabel">Calcul du CBM par colis</h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Colis</th>
                                    <th scope="col">Longueur (cm)</th>
                                    <th scope="col">Largeur (cm)</th>
                                    <th scope="col">Hauteur (cm)</th>
                                    <th scope="col">CBM</th>
                                </tr>
                            </thead>
                            <tbody id="lignesCalculPoids"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" onclick="totaliserCBM()">Total CBM</button>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @php
    // dd($tarifGrille);
    @endphp
    <script>
        // Grille des taux de change transmis depuis le contrôleur
        const tarifGrille = @json($tarifGrille ?? []);
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
            const whatsap = $('#modal_whatsap').val().trim();
            const alertBox = $('#alertModalActeur');

            if (!nom || !tel|| !whatsap) {
                alertBox.text("Veuillez renseigner le nom complet, le numéro de téléphone et le numéro WhatsApp.");
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

        function actualiserTarifAutomatique() {
            //idtypcolis_idtypenvoi_idmoyentransport_poids
            const typcolisEl = document.getElementById('idtypcolis');
            const typenvoiEl = document.getElementById('idtypenvoi');
            const typemoyEl = document.getElementById('idmoyentransport'); //iddestination_iddepart
            const iddepartEl = document.getElementById('iddepart');
            const iddestinationEl = document.getElementById('iddestination');
            const poidsEl = document.getElementById('poids');
            const champTarif = document.getElementById('montant');

            const typcolis = typcolisEl ? typcolisEl.value : '0';
            const typenvoi = typenvoiEl ? typenvoiEl.value : '0';
            const typemoyen = typemoyEl ? typemoyEl.value : '0';
            const iddepart = iddepartEl ? iddepartEl.value : '0';
            const iddestination = iddestinationEl ? iddestinationEl.value : '0';
            const poids = poidsEl ? poidsEl.value : '1';
            //    alert('poids '+poids + 'typcolis '+typcolis + 'typenvoi '+typenvoi + 'typemoyen '+typemoyen);

            if (typcolis > 0 && typenvoi > 0 && typemoyen > 0 && poids > 0) {
                const cle = typcolis + '_' + typenvoi + '_' + typemoyen + '_' + iddepart + '_' + iddestination; //+ '_' + poids;
                let tarif = rechercheTarifExpoedition(tarifGrille, cle);
                if (tarif != null) {
                    champTarif.value = (tarif * poids);
                } else {
                    champTarif.value = 0;
                    alert("Aucun tarif n'est défini pour cette expédition ");
                }

            } else {
                champTarif.value = 0;

            }
        }

        function controlerTrajetExpedition(champModifie) {
            const departEl = document.getElementById('iddepart');
            const destinationEl = document.getElementById('iddestination');

            if (!departEl || !destinationEl) return;

            if (departEl.value !== '0' &&
                destinationEl.value !== '0' &&
                departEl.value === destinationEl.value) {
                alert('La ville de départ doit être différente de la ville de destination.');

                const champ = document.getElementById(champModifie);
                champ.value = '0';
                if (typeof jQuery !== 'undefined') {
                    $(champ).trigger('change');
                }
            }

            actualiserTarifAutomatique();
        }

        function gererChampsPaiement() {
            // Optionnel : adapter l'affichage selon l'état
        }

        function calculPoids() {
            const champNombreColis = document.getElementById('nbrcolis');
            const nombreColis = Number((champNombreColis?.value || '').replace(/\D/g, ''));

            if (!Number.isSafeInteger(nombreColis) || nombreColis < 1) {
                alert('Veuillez renseigner un nombre de colis valide.');
                return;
            }

            const tbody = document.getElementById('lignesCalculPoids');
            tbody.innerHTML = '';

            for (let index = 1; index <= nombreColis; index++) {
                const ligne = document.createElement('tr');
                ligne.innerHTML = `
                <th scope="row">${index}</th>
                <td><input type="number" class="form-control dimension-cbm" min="0" step="any" aria-label="Longueur du colis ${index}" oninput="actualiserCBMligne(this)"></td>
                <td><input type="number" class="form-control dimension-cbm" min="0" step="any" aria-label="Largeur du colis ${index}" oninput="actualiserCBMligne(this)"></td>
                <td><input type="number" class="form-control dimension-cbm" min="0" step="any" aria-label="Hauteur du colis ${index}" oninput="actualiserCBMligne(this)"></td>
                <td><input type="text" class="form-control cbm-ligne" aria-label="CBM du colis ${index}" readonly></td>
            `;
                tbody.appendChild(ligne);
            }

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCalculPoids')).show();
        }

        function actualiserCBMligne(champ) {
            const ligne = champ.closest('tr');
            const dimensions = ligne.querySelectorAll('.dimension-cbm');
            const longueur = Number(dimensions[0].value) || 0;
            const largeur = Number(dimensions[1].value) || 0;
            const hauteur = Number(dimensions[2].value) || 0;
            ligne.querySelector('.cbm-ligne').value = formatCBM((longueur * largeur * hauteur) / 1000000);
        }

        function totaliserCBM() {
            const total = Array.from(document.querySelectorAll('.cbm-ligne'))
                .reduce((somme, champ) => somme + (Number(champ.value) || 0), 0);
            const valeurCBM = formatCBM(total);
            const champPoidsB = document.getElementById('poidsB');
            const champPoids = document.getElementById('poids');

            champPoidsB.value = valeurCBM;
            champPoids.value = valeurCBM;
            actualiserTarifAutomatique();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCalculPoids')).hide();
        }

        function formatCBM(valeur) {
            return String(Number(valeur.toFixed(10)));
        }

        function afficherZonesTransport() {
            const selectTransport = document.getElementById('idmoyentransport');
            const zoneBateau = document.getElementById('zonebateau');
            const zoneAvion = document.getElementById('zoneavion');

            if (!selectTransport || !zoneBateau || !zoneAvion) {
                return;
            }

            const libelleTransport = selectTransport.options[selectTransport.selectedIndex] ?
                selectTransport.options[selectTransport.selectedIndex].text
                .trim()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '') :
                '';

            zoneBateau.style.display = libelleTransport.includes('bateau') ? '' : 'none';
            zoneAvion.style.display = libelleTransport.includes('avion') ? '' : 'none';
            if (libelleTransport.includes('bateau')) {
                document.getElementById('idtypenvoi').value = 6;

            }
        }

        // Liaison automatique avec jQuery / Select2 si présent
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ready(function($) {
                $('#iddepart, #iddestination').on('change select2:select', function() {
                    actualiserTauxAutomatique();
                });
            });
        }

        function changeMoyenTransport() {
            afficherZonesTransport();
            actualiserTarifAutomatique();
        }

        function changePoidesA() {
            const poidsA = parseFloat((document.getElementById('poidsA')?.value || '').replace(/\s/g, '').replace(',', '.')) || 0;
            document.getElementById('poids').value = poidsA; // Mettre à jour le champ caché "poids"
            actualiserTarifAutomatique();
        }

        afficherZonesTransport();
    </script>