<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\caisse;
use App\Models\acteur;
use App\Models\adresse;
use App\Models\element;
use App\Models\expedition;
use App\Models\conteneur;
use App\Models\tarifexpedition;

class ExpeditionController extends Controller
{


    public function index(Request $request)
    {
        //  dd($request);
        try {
            $listeRetraits = $request->routeIs('expedition.retraits');
            $titre = $listeRetraits ? 'Retrait des colis prêts' : 'Liste des colis';

            $conditions = [];
            $bindings = [];

            if ((Auth::user()->idrole ?? null) != 1) {
                $adresseCaisse = idadresseCaisseUser();
                $conditions[] = '(e.iddepart = ? OR e.iddestination = ?)';
                $bindings[] = $adresseCaisse;
                $bindings[] = $adresseCaisse;
            }

            foreach (['etat' => 'e.idetat', 'moyentransport' => 'e.idmoyentransport', 'conteneur' => 'e.idconteneur'] as $parameter => $column) {
                if ($request->filled($parameter) && ctype_digit((string) $request->input($parameter)) && $request->input($parameter) != '0') {
                    $conditions[] = $column . ' = ?';
                    $bindings[] = (int) $request->input($parameter);
                }
            }
            foreach (['depart' => 'e.iddepart', 'destination' => 'e.iddestination'] as $parameter => $column) {
                if ($request->filled($parameter) && ctype_digit((string) $request->input($parameter)) && $request->input($parameter) != '0') {
                    $conditions[] = $column . ' = ?';
                    $bindings[] = (int) $request->input($parameter);
                }
            }
            if ($listeRetraits) {
                $etatPretRetrait = element::where('nomtable', 'etat')
                    ->where('libelle', 'Arrivé au magasin')
                    ->value('id');
                $conditions[] = 'e.idetat = ?';
                $bindings[] = $etatPretRetrait ?: 0;
            }
            if ($request->filled('date_debut')) {
                $conditions[] = 'e.datenreg >= ?';
                $bindings[] = $request->input('date_debut') . ' 00:00:00';
            }

            if ($request->filled('date_fin')) {
                $conditions[] = 'e.datenreg <= ?';
                $bindings[] = $request->input('date_fin') . ' 23:59:59';
            }

            // dd($conditions, $bindings);

            $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

            $donnees = collect(DB::select("
                SELECT e.id, e.code, e.datenreg, e.montant, e.marque, e.poids, e.nbrcolis, e.idetat,
                       COALESCE((SELECT SUM(pc.montant) FROM paymentcolis pc WHERE pc.idcolis = e.id), 0) AS totalpaiementcolis,
                       CASE 
                          WHEN e.idetat = 1 THEN '1'
                          ELSE '0'
                       END AS maj,
                       exp.nom as expediteur,
                       rec.nom as recepteur,
                       dem.nom as demarcheur,
                       dep.libelle as villedepart,
                       dest.libelle as villedestination,
                       c.libelle as caisse,
                       el.libelle as etat,
                       mt.libelle as moyentransport,
                       tc.libelle as typecolis,
                       co.numconteneur as conteneur,
                       CONCAT(dep.libelle,' - ',dest.libelle) trajet
                FROM expeditions e
                LEFT JOIN acteurs exp ON exp.id = e.idexpediteur
                LEFT JOIN acteurs rec ON rec.id = e.idrecepteur
                LEFT JOIN acteurs dem ON dem.id = e.iddemarcheur
                LEFT JOIN adresses dep ON dep.id = e.iddepart
                LEFT JOIN adresses dest ON dest.id = e.iddestination
                LEFT JOIN elements el ON e.idetat = el.id
                LEFT JOIN elements mt ON e.idmoyentransport = mt.id
                LEFT JOIN elements tc ON e.idtypcolis = tc.id
                LEFT JOIN conteneurs co ON e.idconteneur = co.id
                LEFT JOIN caisses c ON c.id = e.idcaisse
                $where
                ORDER BY e.datenreg DESC
            ", $bindings));

            $etats = element::where('nomtable', 'etat')->orderBy('libelle')->get();
            $moyentransports = element::where('nomtable', 'moyentransport')->orderBy('libelle')->get();
            $conteneurs = conteneur::orderBy('numconteneur')->get();
            $adresses = adresse::orderBy('libelle')->get();

            $nouv = $listeRetraits ? null : '/ajout-expedition';
            $entete = 'Date|Code|Trajet|Montant|Statut|Conteneur';
            $cols = 'datenreg|code|trajet|montant|etat|conteneur';
            // $entete = 'Date|Code|Expéditeur|Trajet|Montant|Poids|Statut|Conteneur';
            // $cols = 'datenreg|code|expediteur|trajet|montant|poids|etat|conteneur';
            $btn = $listeRetraits ? 'historique|historique-paiement|retraitcolis' : 'modifier|historique|supprimer|paymentcolis|historique-paiement';
            // $btn = 'modifier|retraitcolis|supprimer|envoicolis|passagedouane|arrivemagasin';

            session(['config' => 'expedition']);
            session(['module' => $listeRetraits ? 'retraitcolis' : 'expedition']);

            return view('back.liste', compact(
                'donnees',
                'titre',
                'nouv',
                'entete',
                'cols',
                'btn',
                'etats',
                'moyentransports',
                'conteneurs',
                'adresses'
            ));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('expedition')->with('error', 'Erreur de chargement : ' . $e->getMessage());
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
    public function form(Request $request)
    {
        $id = $request['idenreg'] ?? null;
        $titre = ($id != null) ? "Modification d'un colis" : "Ajout d'un colis";

        session(['config' => 'expedition']);
        session(['module' => 'expedition']);
        $valider = "enregistrer-expedition";
        // `idtypcolis`, `idtypenvoi`, `idmoyentransport`
        $lenregistrement = ($id != null) ? expedition::find($id) : null;
        $caisses = caisse::orderBy('libelle')->get();
        $acteurs = acteur::orderBy('nom')->get();
        $adresses = adresse::orderBy('libelle')->get();
        $typcolis = element::where('nomtable', 'typecolis')->orderBy('libelle')->get();
        $typenvois = element::where('nomtable', 'typenvoi')->orderBy('libelle')->get();
        $moyentransports = element::where('nomtable', 'moyentransport')->orderBy('libelle')->get();


        // Grille des tarifs d'expédition [idtypcolis_idtypenvoi_idmoyentransport_poids => prix] iddestination_iddepart
        $tarifDb = tarifexpedition::all();
        $tarifGrille = [];
        foreach ($tarifDb as $tx) {
            $tarifGrille[][$tx->idtypcolis . '_' . $tx->idtypenvoi . '_' . $tx->idmoyentransport. '_' . $tx->iddepart . '_' . $tx->iddestination] = $tx->prix . '|' . $tx->poids;
        }
        // dd($tarifGrille,$tarifDb);
        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'valider',
            'caisses',
            'acteurs',
            'adresses',
            'typcolis',
            'typenvois',
            'moyentransports',
            'tarifGrille'

        ));
    }

    public function enregistrer(Request $request)
    {
        try {
            // dd($request);

            // $validated = $request->validate([
            //     'libelle' => ['required', 'string', 'max:255'],
            //     'numconteneur' => ['required', 'string', 'max:255'],
            //     'typeconteneur' => ['required', 'string', 'max:30'],
            //     'coutcfa' => ['required', 'numeric', 'min:0'],
            //     // 'lienconteneur' => ['required', 'string', 'max:255'],
            //     'idconsignataire' => ['required', 'integer', 'exists:elements,id'],
            // ]);

            $id = $request->input('idenreg');

            if ($id == 0 || $id == null) {
                $doub = expedition::where('code', $request->code)->get();
                $e = new expedition();
            } else {
                $doub = expedition::where('code', $request->code)
                    ->where('id', '!=', $id)->get();
                $e = expedition::findOrFail($id);
            }
            // `datenreg`, `montant`, `code`, `marque`, `iddemarcheur`, `idexpediteur`, `idrecepeur`, `idtypcolis`, `idtypenvoi`, `idmoyentransport`, `iddepart`, `iddestination`, `idetat`,
            if (count($doub) == 0) {
                // dd($request->montant);
                $e->code             = $request->code;
                $e->datenreg         = $request->datenreg;
                $e->montant          = (float) sansespace($request->montant);
                $e->marque           = $request->marque;
                $e->poids            = (float) sansespace($request->poids);
                $e->nbrcolis         = (int) sansespace($request->nbrcolis);
                $e->iddemarcheur     = ($request->iddemarcheur && $request->iddemarcheur != '0') ? $request->iddemarcheur : null;
                $e->idexpediteur      = ($request->idexpediteur && $request->idexpediteur != '0') ? $request->idexpediteur : null;
                $e->idrecepteur      = ($request->idrecepteur && $request->idrecepteur != '0') ? $request->idrecepteur : null;
                $e->idmoyentransport = ($request->idmoyentransport && $request->idmoyentransport != '0') ? $request->idmoyentransport : null;
                $e->iddepart         = ($request->iddepart && $request->iddepart != '0') ? $request->iddepart : null;
                $e->iddestination    = ($request->iddestination && $request->iddestination != '0') ? $request->iddestination : null;
                $e->idetat           = ($request->idetat && $request->idetat != '0') ? $request->idetat : 1;
                $e->idtypcolis       = ($request->idtypcolis && $request->idtypcolis != '0') ? $request->idtypcolis : null;
                $e->idtypenvoi       = ($request->idtypenvoi && $request->idtypenvoi != '0') ? $request->idtypenvoi : null;
                $e->idcaisse       = ($request->idcaisse && $request->idcaisse != '0') ? $request->idcaisse : null;
                if ($id == 0 || $id == null) {
                    $e->iduser = Auth::id();
                }

                if ($e->save()) {
                    // if ($id == 0 || $id == null)  Enregistrement opération financière (ENTREE du montant déposé dans la caisse source)
                    if (($e->idcaisse && $e->montdepot > 0) && ($id == 0 || $id == null)) {
                        $rk = "SELECT id FROM operationfinancieres WHERE idcaisse = '" . $e->idcaisse . "' AND libsrce = 'expeditions' AND idsrce = '" . $e->id . "'";
                        $idop = infoParRekete($rk, 'id');
                        enregistreOperationFinanciere('ENTREE', $e->montdepot, $e->id, 'expeditions', $e->idcaisse, $idop);
                    }

                    return redirect()->route('expedition');
                } else {
                    return redirect()->route('expedition')->with('error', 'Erreur d\'enregistrement de l\'expédition.');
                }
            } else {
                $info = "Une expédition avec ce code (" . $request->code . ") existe déjà.";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('expedition')->with('error', 'Erreur d\'enregistrement : ' . $e->getMessage());
        }
    }

    public function supprimer($id)
    {
        try {
            $e = expedition::findOrFail($id);

            // Supprimer les opérations financières rattachées
            DB::table('operationfinancieres')
                ->where('libsrce', 'expeditions')
                ->where('idsrce', $id)
                ->delete();

            $e->delete();
            return redirect()->route('expedition');
        } catch (\Exception $e) {
            return redirect()->route('expedition')->with('error', 'Erreur de suppression : ' . $e->getMessage());
        }
    }
}
