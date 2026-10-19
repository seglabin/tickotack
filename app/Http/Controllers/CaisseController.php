<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\caisse;

class CaisseController extends Controller
{
 
        public function index(Request $request)
    {
        try {
            $titre = "Liste des caisses";
           // $donnees = caisse::orderBy('libelle')->get();
            $donnees = collect(DB::select("
            SELECT c.id, c.libelle, c.montant, c.devise, a.libelle as adresse
            FROM caisses c
            LEFT JOIN adresses a ON a.id = c.idadresse
            ORDER BY c.libelle 
            "));
            $nouv = '/ajout-caisse';
            $entete = 'ibellé|Devise|Solde|Adresse';
            $cols = 'libelle|devise|montant|adresse';
            $btn = 'modifier|supprimer';

            session(['config' => 'caisse']);
            session(['module' => 'caisses']);
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
            return redirect()->route('caisse')->with('error', 'Une erreur est survenue lors du chargement : ' . $e->getMessage());
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
        $titre = ($id != null) ? "Modification d'une caisse" : "Ajout d'une caisse";
        session(['config' => 'caisse']);
        session(['module' => 'caisses']);
        $valider = "enregistrer-caisse";
        $adresses = DB::table('adresses')->orderBy('libelle')->get();

        $lenregistrement = ($id != null) ? caisse::find($id) : null;
        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'valider',
            'adresses'
        ));
    }

    public function enregistrer(Request $request)
    {
        try {
            $id = $request->input('idenreg');
            if ($id == 0 || $id == null) {
                $doub = caisse::where('libelle', $request->libelle)->get();
                $e = new caisse();
            } else {
                $doub = caisse::where('libelle', $request->libelle)
                    ->where('id', '!=', $id)->get();
                $e = caisse::findOrFail($id);
            }

            if (count($doub) == 0) {
                $e->libelle = $request->libelle;
                $e->idadresse = $request->idadresse;
                // $e->montant = $request->montant;
                $e->devise = $request->devise;
                $e->save();
                return redirect()->route('caisse');
            } else {
                $info = "La caisse que vous tentez d'enregistrer existe déjà.";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->route('caisse')->with('error', 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function supprimer($id)
    {
        try {
            // Contrôle d'intégrité référentielle
            $hasTransferts = DB::table('transferts')
                ->where('idcaisse', $id)
                ->exists();

            if (!$hasTransferts) {
                $e = caisse::findOrFail($id);
                $e->delete();
                return redirect()->route('caisse');
            } else {
                $info = "Cette caisse est liée à au moins un transfert existant.\nVous ne pouvez donc pas la supprimer.";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            return redirect()->route('caisse')->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }
}
