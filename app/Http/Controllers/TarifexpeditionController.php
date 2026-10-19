<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\tarifexpedition;
use App\Models\element;
use App\Models\adresse;

class TarifexpeditionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $titre = "Liste des tarif d'expedition";

            $donnees = collect(DB::select("
                SELECT a.id, a.poids, a.prix, tc.libelle as typecolis, mt.libelle as moyen, tn.libelle as typenvoi,
                       dep.libelle as depart, dest.libelle as destination
                FROM tarifexpeditions a
                LEFT JOIN elements tc ON tc.id = a.idtypcolis
                LEFT JOIN elements mt ON mt.id = a.idmoyentransport
                LEFT JOIN elements tn ON tn.id = a.idtypenvoi
                LEFT JOIN adresses dep ON dep.id = a.iddepart
                LEFT JOIN adresses dest ON dest.id = a.iddestination
                ORDER BY moyen
            "));

            $nouv = '/ajout-tarifexpedition';
            $entete = 'Type de colis|Type d\'envoi|Moyen de transport|Départ|Destination|Poids|Prix';
            $cols = 'typecolis|typenvoi|moyen|depart|destination|poids|prix';
            $btn = 'modifier|supprimer';

            session(['config' => 'tarifexpedition']);
            session(['module' => 'tarifexpedition']);
            return view('back.liste', compact(
                'donnees',
                'titre',
                'nouv',
                'entete',
                'cols',
                'btn'
            ));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('tarifexpedition')->with('error', 'Erreur de chargement : ' . $e->getMessage());
        }
    }

    public function formModif(Request $request, $id)
    {
        $request['idenreg'] = $id;
        return $this->form($request);
    }

    public function form(Request $request)
    {
        $id = $request['idenreg'] ?? null;
        $titre = ($id != null) ? "Modification d'un tarif d'expedition" : "Ajout d'un tarif d'expedition";
        session(['config' => 'tarifexpedition']);
        session(['module' => 'tarifexpedition']);
        $valider = "enregistrer-tarifexpedition";

        //`poids`, `prix`, `idtypcolis`, `idtypenvoi`, `idmoyentransport`

        $lenregistrement = ($id != null) ? tarifexpedition::find($id) : null;
        $typecolis  = element::where('nomtable', 'typecolis')->orderBy('libelle')->get();
        $typenvois = element::where('nomtable', 'typenvoi')->orderBy('libelle')->get();
        $moyens = element::where('nomtable', 'moyentransport')->orderBy('libelle')->get();
        $adresses = adresse::orderBy('libelle')->get();

        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'valider',
            'typecolis',
            'typenvois',
            'moyens',
            'adresses'
        ));
    }

    public function enregistrer(Request $request)
    {
        // dd($request);
        try {
            if ($request->filled('iddepart')
                && $request->filled('iddestination')
                && $request->iddepart !== '0'
                && $request->iddestination !== '0'
                && $request->iddepart == $request->iddestination) {
                $info = "La ville de départ doit être différente de la ville de destination.";
                $titre = "DONNEES INVALIDES";
                return view('alertDoublon', compact('info', 'titre'));
            }

            $id = $request->input('idenreg');
            if ($id == 0 || $id == null) {
                $doub = tarifexpedition::where('idtypcolis', $request->idtypcolis)
                    ->where('idtypenvoi', $request->idtypenvoi)
                    ->where('idmoyentransport', $request->idmoyentransport)
                    ->where('iddepart', $request->iddepart)
                    ->where('iddestination', $request->iddestination)
                    // ->where('poids', $request->poids)
                    ->get();
                $e = new tarifexpedition();
            } else {
                $doub = tarifexpedition::where('idtypcolis', $request->idtypcolis)
                    ->where('idtypenvoi', $request->idtypenvoi)
                    ->where('idmoyentransport', $request->idmoyentransport)
                    ->where('iddepart', $request->iddepart)
                    ->where('iddestination', $request->iddestination)
                    // ->where('poids', $request->poids)
                    ->where('id', '!=', $id)->get();
                $e = tarifexpedition::findOrFail($id);
            }

            if (count($doub) == 0) {
                //`poids`, `prix`, `idtypcolis`, `idtypenvoi`, `idmoyentransport`
                // $e->poids        = $request->poids;
                $e->prix        = sansespace($request->prix);
                $e->idmoyentransport = ($request->idmoyentransport && $request->idmoyentransport != '0') ? $request->idmoyentransport : null;
                $e->idtypcolis = ($request->idtypcolis && $request->idtypcolis != '0') ? $request->idtypcolis : null;
                $e->idtypenvoi = ($request->idtypenvoi && $request->idtypenvoi != '0') ? $request->idtypenvoi : null;
                $e->iddepart = ($request->iddepart && $request->iddepart != '0') ? $request->iddepart : null;
                $e->iddestination = ($request->iddestination && $request->iddestination != '0') ? $request->iddestination : null;
                $e->save();
                return redirect()->route('tarifexpedition');
            } else {
                $info = "Le tarif que vous tentez d'enregistrer existe déjà (même date, source et caisse).";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('tarifexpedition')->with('error', 'Erreur d\'enregistrement : ' . $e->getMessage());
        }
    }

    public function supprimer($id)
    {
        try {
            $hasTransferts = DB::table('transferts')
                ->where('idexpediteur', $id)
                ->orWhere('idrecepteur', $id)
                ->exists();

            if (!$hasTransferts) {
                $e = tarifexpedition::findOrFail($id);
                $e->delete();
                return redirect()->route('tarifexpedition');
            } else {
                $info = "Ce tarif est lié à au moins un transfert.\nVous ne pouvez pas le supprimer.";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            return redirect()->route('tarifexpedition')->with('error', 'Erreur de suppression : ' . $e->getMessage());
        }
    }
}
