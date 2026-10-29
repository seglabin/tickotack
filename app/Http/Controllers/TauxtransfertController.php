<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\tauxtransfert;
use App\Models\adresse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TauxtransfertController extends Controller
{
    
        public function index(Request $request)
    {
        try {            
            $titre = "Liste des taux de change";

            $donnees = collect(DB::select("
                SELECT a.id, a.taux, a.iddepart, a.iddestination,a.operateur, s.libelle as depart, c.libelle as destination
                FROM tauxtransferts a
                LEFT JOIN adresses s ON s.id = a.iddepart
                LEFT JOIN adresses c ON c.id = a.iddestination
                ORDER BY depart desc
            "));

            $nouv = '/ajout-tauxtransfert';
            $entete = 'Départ|Destination|Taux|Opérateur';
            $cols = 'depart|destination|taux|operateur';
            $btn = 'modifier|supprimer';

            session(['config' => 'tauxtransfert']);
            session(['module' => 'tauxtransfert']);
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
            return redirect()->route('tauxtransfert')->with('error', 'Erreur de chargement : ' . $e->getMessage());
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
        $titre = ($id != null) ? "Modification d'un taux de change" : "Ajout d'un taux de change";
        session(['config' => 'tauxtransfert']);
        session(['module' => 'tauxtransfert']);
        $valider = "enregistrer-tauxtransfert";

        $lenregistrement = ($id != null) ? tauxtransfert::find($id) : null;
      
        $departs = adresse::orderBy('libelle')->orderByDesc('libelle')->get();
        $destinations = adresse::orderBy('libelle')->orderByDesc('libelle')->get();

        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'valider',
            'departs',
            'destinations'
        ));
    }

    public function enregistrer(Request $request)
    {
       // dd($request);
       
        try {
            $id = $request->input('idenreg');
            if ($id == 0 || $id == null) {
                $doub = tauxtransfert::where('iddepart', $request->iddepart)
                    ->where('iddestination', $request->iddestination)
                    ->get();
                $e = new tauxtransfert();
            } else {
                $doub = tauxtransfert::where('iddepart', $request->iddepart)
                    ->where('iddestination', $request->iddestination)
                    ->where('id', '!=', $id)->get();
                $e = tauxtransfert::findOrFail($id);
            }
            

            if (count($doub) == 0) {
                $e->iddepart        = $request->iddepart;
                $e->iddestination        = $request->iddestination;
                $e->taux   = sansespace($request->taux);
                $e->operateur = sansespace($request->operateur);
                if ($id == 0 || $id == null) {
                    $e->iduser = Auth::id();
                }
               if($e->save()){
                return redirect()->route('tauxtransfert');
               }else{
                return redirect()->route('tauxtransfert')->with('error', 'Erreur d\'enregistrement : ' . $e->getMessage());
               }
            } else {
                $info = "Le taux de change que vous tentez d'enregistrer existe déjà (même de     part et destination).";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('tauxtransfert')->with('error', 'Erreur d\'enregistrement : ' . $e->getMessage());
        }
    }

    public function supprimer($id)
    {
        try {
            $hasTransferts = DB::table('tauxtransfert')
                ->where('iddepart', $id)
                ->orWhere('iddestination', $id)
                ->exists();

            if (!$hasTransferts) {
                $e = tauxtransfert::findOrFail($id);
                $e->delete();
                return redirect()->route('tauxtransfert');
            } else {
                $info = "Le taux de change que vous tentez d'enregistrer existe déjà (même de     part et destination).\nVous ne pouvez pas le supprimer.";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            return redirect()->route('tauxtransfert')->with('error', 'Erreur de suppression : ' . $e->getMessage());
        }
    }
}
