<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\approcaisse;
use App\Models\element;
use App\Models\caisse;


class ApprocaisseController extends Controller
{
        public function index(Request $request)
    {
        try {
            $titre = "Liste des approvisionnements des caisses";
            $donnees = collect(DB::select("
            SELECT a.id, a.dateappro, a.montant, a.observation, s.libelle as source, c.libelle as caisse
            FROM approcaisses a
            LEFT JOIN elements s ON s.id = a.idsource
            LEFT JOIN caisses c ON c.id = a.idcaisse
            ORDER BY a.dateappro desc
            "));
            
            $nouv = '/ajout-approcaisse';
            $entete = 'Date|Montant|Observation|Source|Caisse';
            $cols = 'dateappro|montant|observation|source|caisse';
            $btn = 'modifier|supprimer';
            // dd(session()->all());

            session(['config' => 'approcaisse']);
            session(['module' => 'approcaisse']);
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
            return redirect()->route('approcaisse')->with('error', 'Erreur de chargement : ' . $e->getMessage());
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
        $titre = ($id != null) ? "Modification d'un approvisionnement de caisse" : "Ajout d'un approvisionnement de caisse";
        session(['config' => 'approcaisse']);
        session(['module' => 'approcaisse']);
        $valider = "enregistrer-approcaisse";

        $lenregistrement = ($id != null) ? approcaisse::find($id) : null;
        $sourcesele  = element::where('nomtable', 'sourceappro')->orderBy('libelle')->get();
        $caisses = caisse::orderBy('libelle')->get();

        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'valider',
            'sourcesele',
            'caisses'
        ));
    }

    public function enregistrer(Request $request)
    {
       // dd($request);
        try {
            $id = $request->input('idenreg');
            if ($id == 0 || $id == null) {
                $doub = approcaisse::where('dateappro', $request->dateappro)
                    ->where('idsource', $request->idsource)
                    ->where('idcaisse', $request->idcaisse)
                    ->get();
                $e = new approcaisse();
            } else {
                $doub = approcaisse::where('dateappro', $request->dateappro)
                    ->where('idsource', $request->idsource)
                    ->where('idcaisse', $request->idcaisse)
                    ->where('id', '!=', $id)->get();
                $e = approcaisse::findOrFail($id);
            }
            

            if (count($doub) == 0) {
                $e->dateappro        = $request->dateappro;
                $e->montant        = sansespace($request->montant);
                $e->observation    = $request->observation;
                $e->idsource     = $request->idsource;
                $e->idcaisse = ($request->idcaisse && $request->idcaisse != '0') ? $request->idcaisse : null;
               if($e->save()){
               
                $rk = "SELECT id FROM operationfinancieres WHERE idcaisse = '".$request->idcaisse."' AND libsrce = 'approcaisses' AND idsrce = '".$e->id."'";
                $idop = infoParRekete($rk, 'id');
                
                enregistreOperationFinanciere('ENTREE', sansespace($request->montant), $e->id, 'approcaisses', $request->idcaisse, $idop);
                
                return redirect()->route('approcaisse');
               }else{
                return redirect()->route('approcaisse')->with('error', 'Erreur d\'enregistrement : ' . $e->getMessage());
               }
            } else {
                $info = "L'approvisionnement que vous tentez d'enregistrer existe déjà (même date, source et caisse).";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('approcaisse')->with('error', 'Erreur d\'enregistrement : ' . $e->getMessage());
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
                $e = approcaisse::findOrFail($id);
                $e->delete();
                return redirect()->route('approcaisse');
            } else {
                $info = "Cet approvisionnement est lié à au moins un transfert.\nVous ne pouvez pas le supprimer.";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            return redirect()->route('approcaisse')->with('error', 'Erreur de suppression : ' . $e->getMessage());
        }
    }
}
