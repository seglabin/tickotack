<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\moyentransfert;

class MoyentransfertsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $titre = "Liste des moyens de transfert";
            $donnees = moyentransfert::orderBy('libelle')->get();
            $nouv = '/ajout-moyentransferts';
            $entete = 'Libellé|Numéro';
            $cols = 'libelle|numero';
            $btn = 'modifier|supprimer';

            session(['config' => 'moyentransferts']);
            session(['module' => 'moyentransferts']);
            return view('back.liste', compact(
                'donnees',
                'titre',
                'nouv',
                'entete',
                'cols',
                'btn'
            ));
        } catch (\Exception $e) {
            return redirect()->route('moyentransferts')->with('error', 'Une erreur est survenue lors du chargement de la page.');
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
        $titre = ($id != null) ? "Modification d'un moyen de transfert" : "Ajout d'un moyen de transfert";
        session(['config' => 'moyentransferts']);
        session(['module' => 'moyentransferts']);
        $valider = "enregistrer-moyentransferts";

        $lenregistrement = ($id != null) ? moyentransfert::find($id) : null;
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
                $doub = moyentransfert::where('libelle', $request->libelle)->get();
                $e = new moyentransfert();
            } else {
                $doub = moyentransfert::where('libelle', $request->libelle)
                    ->where('id', '!=', $id)->get();
                $e = moyentransfert::findOrFail($id);
            }

            if (count($doub) == 0) {
                $e->libelle = $request->libelle;
                $e->numero = $request->numero;
                $e->save();
                return redirect()->route('moyentransferts');
            } else {
                $info = "Le moyen de transfert que vous tentez d'enregistrer existe déjà.";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            return redirect()->route('moyentransferts')->with('error', 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function supprimer($id)
    {
        try {
            // Contrôle d'intégrité référentielle
            $hasTransferts = DB::table('transferts')->where('idmoyentransfert', $id)->exists();

            if (!$hasTransferts) {
                $e = moyentransfert::findOrFail($id);
                $e->delete();
                return redirect()->route('moyentransferts');
            } else {
                $info = "Ce moyen de transfert est lié à au moins un transfert existant.\nVous ne pouvez donc pas le supprimer.";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            return redirect()->route('moyentransferts')->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }
}
