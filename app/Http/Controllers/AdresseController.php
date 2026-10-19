<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\adresse;

class AdresseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $titre = "Liste des adresses / villes";
            $donnees = adresse::orderBy('libelle')->get();
            $nouv = '/ajout-adresse';
            $entete = 'Ville / Libellé|Pays|Devise';
            $cols = 'libelle|pays|devise';
            $btn = 'modifier|supprimer';

            session(['config' => 'adresse']);
            session(['module' => 'adresses']);
            return view('back.liste', compact(
                'donnees',
                'titre',
                'nouv',
                'entete',
                'cols',
                'btn'
            ));
        } catch (\Exception $e) {
            return redirect()->route('adresse')->with('error', 'Une erreur est survenue lors du chargement : ' . $e->getMessage());
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
        $titre = ($id != null) ? "Modification d'une adresse" : "Ajout d'une adresse";
        session(['config' => 'adresse']);
        session(['module' => 'adresses']);
        $valider = "enregistrer-adresse";

        $lenregistrement = ($id != null) ? adresse::find($id) : null;
        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'valider'
        ));
    }

    public function enregistrer(Request $request)
    {
        try {
            $id = $request->input('idenreg');
            if ($id == 0 || $id == null) {
                $doub = adresse::where('libelle', $request->libelle)
                    ->where('pays', $request->pays)->get();
                $e = new adresse();
            } else {
                $doub = adresse::where('libelle', $request->libelle)
                    ->where('pays', $request->pays)
                    ->where('id', '!=', $id)->get();
                $e = adresse::findOrFail($id);
            }

            if (count($doub) == 0) {
                $e->libelle = $request->libelle;
                $e->pays = $request->pays;
                $e->devise = $request->devise;
                $e->save();
                return redirect()->route('adresse');
            } else {
                $info = "L'adresse que vous tentez d'enregistrer existe déjà.";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            return redirect()->route('adresse')->with('error', 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function supprimer($id)
    {
        try {
            // Contrôle d'intégrité référentielle
            $hasTransferts = DB::table('transferts')
                ->where('iddepart', $id)
                ->orWhere('iddestination', $id)
                ->exists();

            if (!$hasTransferts) {
                $e = adresse::findOrFail($id);
                $e->delete();
                return redirect()->route('adresse');
            } else {
                $info = "Cette adresse est liée à au moins un transfert existant.\nVous ne pouvez donc pas la supprimer.";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            return redirect()->route('adresse')->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }
}
