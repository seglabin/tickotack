<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\transfert;
use App\Models\caisse;
use App\Models\acteur;
use App\Models\adresse;
use App\Models\moyentransfert;
use App\Models\element;
use App\Models\tauxtransfert;
use Illuminate\Support\Facades\Auth;

class TransfertController extends Controller
{
    public function index(Request $request)
    {
        try {
            $listeRetraits = $request->routeIs('transfert.retraits');
            $titre = $listeRetraits ? 'Retrait des transferts prêts' : 'Liste des transferts';
            $idadrescaisseuser = idadresseCaisseUser();
            $iduserRole = Auth::user()->idrole ?? null;

            $conditions = [];
            $bindings = [];


            // $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
            $criteres = "";

            if ($iduserRole != 1) {
                $criteres .= " WHERE ";
                $criteres .= " ( t.iddestination = '" . $idadrescaisseuser . "' OR t.iddepart='" . $idadrescaisseuser . "' ) ";
            }
            if ($listeRetraits) {
                $criteres .= $criteres != "" ? " AND " : " WHERE ";
                $criteres .= " t.datepayement IS NULL AND t.idetat = 1";
            }
            if ($request->filled('date_debut')) {
                $criteres .= $criteres != "" ? " AND " : " WHERE ";
                $criteres .= "  t.datetransfert >= '" . $request->input('date_debut') . " 00:00:00'";
            }

            if ($request->filled('date_fin')) {
                $criteres .= $criteres != "" ? " AND " : " WHERE ";
                $criteres .= "  t.datetransfert <= '" . $request->input('date_fin') . " 23:59:59'";
            }

            if ($request->input('statut') === 'encours') {
                $criteres .= $criteres != "" ? " AND " : " WHERE ";
                $criteres .= "  t.datepayement IS NULL";
            } elseif ($request->input('statut') === 'recu') {
                $criteres .= $criteres != "" ? " AND " : " WHERE ";
                $criteres .= " t.datepayement IS NOT NULL";
            }


            if ($request->filled('depart') && ctype_digit((string) $request->input('depart'))) {
                $criteres .= $criteres != "" ? " AND " : " WHERE ";
                $criteres .= " t.iddepart = '" . (int) $request->input('depart') . "'";
            }

            if ($request->filled('destination') && ctype_digit((string) $request->input('destination'))) {

                $criteres .= $criteres != "" ? " AND " : " WHERE ";
                $criteres .= " t.iddestination = '" . (int) $request->input('destination') . "'";
            }

            // dd($criteres);
            $donnees = transfertParCritere($criteres, Auth::id(), (int) $iduserRole === 1);
            // dd($d);
          
            $adresses = adresse::orderBy('libelle')->get();

            $nouv = $listeRetraits ? null : '/ajout-transfert';
            $entete = 'Date|Code|Expéditeur|Trajet|Montant Dépôt|Montant Retrait'; //|Caisse Récepteur|
            $cols = 'datetransfert|code|expediteur|trajet|montdepot|montretrait'; //|caisserecepteur|
            $btn = $listeRetraits ? 'retraitransfert' : 'modifier|recu|supprimer';

            session(['config' => 'transfert']);
            session(['module' => $listeRetraits ? 'retraittransfert' : 'transfert']);

            // dd($donnees);

            return view('back.liste', compact(
                'donnees',
                'titre',
                'nouv',
                'entete',
                'cols',
                'btn',
                'adresses'
            ));
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->route('transfert')->with('error', 'Erreur de chargement : ' . $e->getMessage());
        }
    }

    public function retraits(Request $request)
    {
        return $this->index($request);
    }

    public function formModif(Request $request, $id)
    {
        $request['idenreg'] = $id;
        return $this->form($request);
    }

    public function retraitransfert(Request $request, $id)
    {
        $request['idenreg'] = $id;
        return $this->form($request);
    }

    public function apiRetraitransfert($id)
    {
        // dd($id);
        try {

            $transfert = collect(DB::select("
                  SELECT t.id, t.code code, t.datetransfert, t.montdepot, t.montretrait, t.montpaye, t.taux,t.datepayement, observation,
                       exp.nom as expediteur,
                      rec.nom as recepteur,
                      rec.carte as carte_recepteur,
                       dep.libelle as villedepart,
                       dest.libelle as villedestination,
                       CONCAT(dep.libelle,' - ',dest.libelle) trajet,
                       m.libelle as moyen,
                       e.libelle as etat
                FROM transferts t
                LEFT JOIN acteurs exp ON exp.id = t.idexpediteur
                LEFT JOIN acteurs rec ON rec.id = t.idrecepteur
                LEFT JOIN adresses dep ON dep.id = t.iddepart
                LEFT JOIN adresses dest ON dest.id = t.iddestination
                LEFT JOIN moyentransferts m ON m.id = t.idmoyentransfert
                LEFT JOIN elements e ON e.id = t.idetat
                WHERE t.id = ?;
            ", [$id]))->first();
            // $transfert = transfert::find($id);
            // dd($transfert);
            if (!$transfert) {
                return '<div class="alert alert-danger">Transfert introuvable.</div>';
            }
            if (!$this->userCanModifyRecord(transfert::findOrFail($id))) {
                return response('<div class="alert alert-danger">Vous ne pouvez pas retirer ce transfert.</div>', 403);
            }


            $html = "<div class='d-flex justify-content-between align-items-center mb-3'>";
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-6'><h6>Transfert : " . $transfert->code . "</h6> </div>";
            $html .= "<div class='col-md-6'> <h6>Trajet : " . $transfert->trajet . "</h6> </div>";
            $html .= "<div class='col-md-6'> <h6>Expéditeur : " . $transfert->expediteur . "</h6> </div>";
            $html .= "<div class='col-md-6'> <h6>Récepteur : " . $transfert->recepteur . "</h6> </div>";
            $html .= "<div class='col-md-6'> <h6>Montant Retrait : " . $transfert->montretrait . "</h6> </div>";
            $html .= "<div class='col-md-6'>
            <label for='montpaye' class='form-label'>Montant payé (CFA) (*)</label>
            <input type='number' class='form-control' id='montpaye' name='montpaye' min='0' step='0.01' required value='" . $transfert->montretrait . "'>
            </div>";
            $html .= "<div class='col-md-6'> 
            <label for='datepayement' class='form-label'>Date paiement </label>
            <input type='datetime-local' class='form-control' id='datepayement' name='datepayement' value='" . ($transfert->datepayement ? date('Y-m-d\TH:i', strtotime($transfert->datepayement)) : date('Y-m-d\TH:i')) . "'>
            </div>";
            $html .= "<div class='col-md-6'> 
            <label for='observation' class='form-label'>Motif retrait</label>
            <input type='text' class='form-control' id='observation' name='observation' value='" . $transfert->observation . "'>
            </div>";
            $photoCarte = $transfert->carte_recepteur
                ? asset('storage/images/Acteurs/' . $transfert->carte_recepteur)
                : '';
            $html .= "<div class='col-md-6'>
            <label for='carterecepteur' class='form-label'>Carte du récepteur</label>
            <div class='control-group'>
                <div class='fileupload fileupload-new' data-provides='fileupload'>
                    <div class='fileupload-preview thumbnail' style='width: 120px; height: 120px;'>" .
                ($photoCarte ? "<img src='" . $photoCarte . "' style='max-width: 120px; max-height: 120px;'>" : '') .
                "</div>
                    <div>
                        <span class='btn btn-file btn-small " . ($photoCarte ? 'btn-warning' : 'btn-success') . "' title='Choisir'>
                            <span class='fileupload-new'><i class='fa fa-camera icon-white'></i></span>
                            <span class='fileupload-exists btn-small btn-warning' title='Changer'><i class='fa fa-camera'></i></span>
                            <input type='file' name='carte_recepteur' id='carte_recepteur' accept='image/*'>
                        </span>
                        <span class='btn btn-small btn-danger' data-dismiss='fileupload' title='Supprimer'>
                            <i class='fa fa-trash'></i>
                        </span>
                    </div>
                </div>
            </div>
            </div>";


            $html .= "</div>";

            $html .= "<input type='hidden' name='idtransfert' id='idtransfert' value='" . $transfert->id . "'>";
            $html .= "<input type='hidden' name='datetransfert' id='datetransfert' value='" . $transfert->datetransfert . "'>";
            $html .= "</div>";
            $html .= "<div class='row'>";
            $html .= "<button type='button' onclick='valideretraitransfert(" . $transfert->id . ")' class='btn btn-primary' >
            Valider le retrait
            </button>";
            $html .= "</div>";

            $html .= "</div>";

            // $html .= "</div>";

            return $html;
        } catch (\Exception $e) {
            return '<div class="alert alert-danger">Une erreur est survenue : ' . $e->getMessage() . '</div>';
        }
    }

    public function recu($id)
    {
        $transfert = collect(DB::select("
            SELECT t.id, t.code, t.datetransfert, t.datepayement, t.montdepot, t.montretrait, t.montpaye,
                   t.taux, t.observation, rec.carte AS carte_recepteur,
                   exp.nom AS expediteur, rec.nom AS recepteur,
                   dep.libelle AS villedepart, dest.libelle AS villedestination,
                   m.libelle AS moyen, c.libelle AS caisse, e.libelle AS etat
            FROM transferts t
            LEFT JOIN acteurs exp ON exp.id = t.idexpediteur
            LEFT JOIN acteurs rec ON rec.id = t.idrecepteur
            LEFT JOIN adresses dep ON dep.id = t.iddepart
            LEFT JOIN adresses dest ON dest.id = t.iddestination
            LEFT JOIN moyentransferts m ON m.id = t.idmoyentransfert
            LEFT JOIN caisses c ON c.id = t.idcaisse
            LEFT JOIN elements e ON e.id = t.idetat
            WHERE t.id = ?
        ", [$id]))->first();

        abort_unless($transfert, 404, 'Transfert introuvable.');

        return view('back.transfert-recu', compact('transfert'));
    }

    public function validerRetrait(Request $request)
    {
        $id = $request->input('idtransfert');
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Identifiant du transfert manquant.'], 422);
        }

        $transfert = transfert::find($id);
        if (!$transfert) {
            return response()->json(['success' => false, 'message' => 'Transfert introuvable.'], 404);
        }
        if (!$this->userCanModifyRecord($transfert)) {
            return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas retirer ce transfert.'], 403);
        }

        try {
            $datepayement  = $request->input('datepayement');
            $observation   = $request->input('observation');

            if (!$datepayement) {
                return response()->json(['success' => false, 'message' => 'La date de paiement est obligatoire.'], 422);
            }
            $montpaye = $request->input('montpaye');
            if ($montpaye === null || $montpaye === '' || !is_numeric($montpaye) || (float) $montpaye < 0) {
                return response()->json(['success' => false, 'message' => 'Le montant payé doit être un nombre positif ou nul.'], 422);
            }

            $request->validate([
                'carte_recepteur' => ['nullable', 'image', 'max:5120'],
            ], [
                'carte_recepteur.image' => 'La carte du récepteur doit être une image.',
                'carte_recepteur.max' => 'L\'image de la carte ne doit pas dépasser 5 Mo.',
            ]);

            $recepteur = acteur::findOrFail($transfert->idrecepteur);

            if ($request->hasFile('carte_recepteur')) {
                $destinationPath = 'storage/images/Acteurs/';
                File::ensureDirectoryExists(public_path($destinationPath), 0755, true);
                $ancienneCarte = $recepteur->carte;
                $recepteur->carte = saveFichier($request, 'carte_recepteur', $destinationPath, 'CARTE_ACT_');
                $recepteur->save();

                if ($ancienneCarte && $ancienneCarte !== $recepteur->carte) {
                    $ancienChemin = public_path($destinationPath . $ancienneCarte);
                    if (File::exists($ancienChemin)) {
                        File::delete($ancienChemin);
                    }
                }
            }

            // Récupérer l'état "Payé" / "Retiré" dans la table elements
            // $etatPaye = element::where('nomtable', 'etat')
            //     ->where(function ($q) {
            //         $q->where('libelle', 'like', '%Pay%')
            //           ->orWhere('libelle', 'like', '%Retir%');
            //     })
            $etatPaye = element::where('nomtable', 'etat')
                ->where(function ($q) {
                    $q->where('libelle', 'like', '%Pay%')
                        ->orWhere('libelle', 'like', '%Retir%');
                })
                ->orderBy('id')
                ->first();

            $transfert->datepayement = $datepayement;
            $transfert->montpaye = (float) $montpaye;
            $transfert->observation  = $observation ?: $transfert->observation;
            if ($etatPaye) {
                $transfert->idetat = $etatPaye->id;
            }
            if ($transfert->save()) {
                //maj caisse
                //Ici on utilise la caisse de la destination
                $u = Auth::user();
                if($u->idrole != 1){ //Puisque c'est seulement les transferts destinés à la caisse de l'utilisateur qui peuvent être validés
                    $idcais = $u->idcaisse;
                }else{
                    $idcais = infoParRekete("SELECT id FROM caisses WHERE idadresse = '" . $transfert->iddestination . "'", 'id');
                    
                }   
                // $idcais = ($u->idcaisse) != null ? $u->idcaisse : $transfert->idcaisse;

                //    print_r($u,$idcais);
                if (($idcais != null && $transfert->montpaye > 0)) {
                    $rk = "SELECT id FROM operationfinancieres WHERE idcaisse = '" . $idcais . "' AND libsrce = 'transferts' AND idsrce = '" . $transfert->id . "' AND sens = 'Sortie'";
                    $idop = infoParRekete($rk, 'id');
                    // dd($rk);
                    enregistreOperationFinanciere('Sortie', $transfert->montpaye, $transfert->id, 'transferts', $idcais, $idop);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Retrait validé avec succès.',
                'receipt_url' => url('/transfert/' . $transfert->id . '/recu-retrait-transfert'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    public function form(Request $request)
    {
        $id = $request['idenreg'] ?? null;
        $titre = ($id != null) ? "Modification d'un transfert" : "Ajout d'un transfert";

        session(['config' => 'transfert']);
        session(['module' => 'transfert']);
        $valider = "enregistrer-transfert";

        $lenregistrement = ($id != null) ? transfert::findOrFail($id) : null;
        if ($lenregistrement) {
            $this->authorizeRecordOwner($lenregistrement);
        }
      
         $operaeur =  null;
       if($lenregistrement != null){
        $rek = "SELECT operateur FROM tauxtransferts WHERE iddepart = '".$lenregistrement->iddepart."' AND iddestination = '".$lenregistrement->iddestination."'";
        $operaeur = infoParRekete($rek, 'operateur');
       }

        $caisses = caisse::select('id', 'libelle', 'idadresse')->orderBy('libelle')->get();
        $acteurs = acteur::orderBy('nom')->get();
        $adresses = adresse::orderBy('libelle')->get();
        $moyentransferts = moyentransfert::orderBy('libelle')->get();

        // Récupération des états (nomtable = 'etat')
        $etats = element::where('nomtable', 'etat')->orderBy('libelle')->get();
        // if ($etats->isEmpty()) {
        //     // Créer les états par défaut si la table elements n'en a pas encore
        //     $etatsDefauts = ['Initié', 'En cours', 'Payé', 'Annulé'];
        //     foreach ($etatsDefauts as $lib) {
        //         element::firstOrCreate(['nomtable' => 'etat', 'libelle' => $lib]);
        //     }
        //     $etats = element::where('nomtable', 'etat')->orderBy('libelle')->get();
        // }

        // Grille des taux de change [depart_destination => taux]
        $tauxDb = tauxtransfert::all();
        $tauxGrille = [];
        foreach ($tauxDb as $tx) {
            $tauxGrille[$tx->iddepart . '_' . $tx->iddestination] = $tx->taux . '|' . $tx->operateur;
        }

        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'valider',
            'caisses',
            'acteurs',
            'adresses',
            'moyentransferts',
            'etats',
            'tauxGrille',
            'operaeur'
        ));
    }

    public function enregistrer(Request $request)
    {
        $id = $request->input('idenreg');
        $existing = ($id != 0 && $id != null) ? transfert::findOrFail($id) : null;
        if ($existing) {
            $this->authorizeRecordOwner($existing);
        }

        try {
            $idcaisse = ($request->idcaisse && $request->idcaisse != '0') ? $request->idcaisse : null;
            $iddepart = $idcaisse
                ? caisse::find($idcaisse)?->idadresse
                : (($request->iddepart && $request->iddepart != '0') ? $request->iddepart : null);

            if (
                $iddepart
                && $request->filled('iddestination')
                && $request->iddestination !== '0'
                && (string) $iddepart === (string) $request->iddestination
            ) {
                return view('alertDoublon', [
                    'info' => 'La ville de départ doit être différente de la ville de destination.',
                    'titre' => 'DONNEES INVALIDES',
                ]);
            }

            if ($id == 0 || $id == null) {
                $doub = transfert::where('code', $request->code)->get();
                $e = new transfert();
            } else {
                $doub = transfert::where('code', $request->code)
                    ->where('id', '!=', $id)->get();
                $e = $existing;
            }

            if (count($doub) == 0) {
                $e->code             = $request->code;
                $e->datetransfert    = $request->datetransfert;
                $e->montdepot        = (float) sansespace($request->montdepot);
                $e->montretrait      = (float) sansespace($request->montretrait);
                $e->taux             = (float) str_replace(',', '.', sansespace($request->taux ?? '1'));
                $e->description      = $request->description;
                $e->observation      = ($request->observation && $request->observation != '') ? $request->observation : null;
                $e->datepayement     = $request->datepayement ?: null;

                $e->idexpediteur     = ($request->idexpediteur && $request->idexpediteur != '0') ? $request->idexpediteur : null;
                $e->idrecepteur      = ($request->idrecepteur && $request->idrecepteur != '0') ? $request->idrecepteur : null;
                $e->idmoyentransfert = ($request->idmoyentransfert && $request->idmoyentransfert != '0') ? $request->idmoyentransfert : null;
                $e->iddestination    = ($request->iddestination && $request->iddestination != '0') ? $request->iddestination : null;
                $e->idetat           = ($request->idetat && $request->idetat != '0') ? $request->idetat : 1;
                $e->idcaisse         = $idcaisse;
                $e->iddepart         = $iddepart;
                if ($id == 0 || $id == null) {
                    $e->iduser = Auth::id();
                }

                if ($e->save()) {
                    // if ($id == 0 || $id == null)  Enregistrement opération financière (ENTREE du montant déposé dans la caisse source)
                    if (($e->idcaisse && $e->montdepot > 0) ) {
                    // if (($e->idcaisse && $e->montdepot > 0) && ($id == 0 || $id == null)) {
                        $rk = "SELECT id FROM operationfinancieres WHERE idcaisse = '" . $e->idcaisse . "' AND libsrce = 'transferts' AND idsrce = '" . $e->id . "'";
                        $idop = infoParRekete($rk, 'id');
                        enregistreOperationFinanciere('ENTREE', $e->montdepot, $e->id, 'transferts', $e->idcaisse, $idop);
                    }

                    return redirect()->route('transfert');
                } else {
                    return redirect()->route('transfert')->with('error', 'Erreur d\'enregistrement du transfert.');
                }
            } else {
                $info = "Un transfert avec ce code (" . $request->code . ") existe déjà.";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            return redirect()->route('transfert')->with('error', 'Erreur d\'enregistrement : ' . $e->getMessage());
        }
    }

    public function supprimer($id)
    {
        $transfert = transfert::findOrFail($id);
        $this->authorizeRecordOwner($transfert);

        try {
            // Supprimer les opérations financières rattachées
            DB::table('operationfinancieres')
                ->where('libsrce', 'transferts')
                ->where('idsrce', $id)
                ->delete();

            $transfert->delete();
            return redirect()->route('transfert');
        } catch (\Exception $e) {
            return redirect()->route('transfert')->with('error', 'Erreur de suppression : ' . $e->getMessage());
        }
    }
}
