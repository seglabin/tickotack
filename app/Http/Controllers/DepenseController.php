<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\depense;
use App\Models\element;
use App\Models\caisse;
use Illuminate\Support\Facades\Auth;

class DepenseController extends Controller
{
         public function index(Request $request)
    {
        try {
            $titre = "Liste des dépenses";
            //`datedepense`, `libelle`, `montantdepense`, `idcaisse`
$u = Auth::user();
$iduser  = $u != null ? $u->id:null;
$idrole  = $u != null ? $u->idrole:null;

$cond = ($idrole != 1)? " WHERE d.iduser = '".$iduser."' ": "";
            $donnees = collect(DB::select("
                SELECT d.id, d.iduser,
                       CASE WHEN d.iduser = ? OR ? = 1 THEN 1 ELSE 0 END AS peutmodifier,
                       d.datedepense, d.montantdepense, d.libelle, c.libelle as caisse
                FROM depenses d
                LEFT JOIN caisses c ON c.id = d.idcaisse
                ".$cond. "                
                ORDER BY d.datedepense desc
            ", [$iduser, (int) $idrole === 1 ? 1 : 0]));

            $nouv = '/ajout-depense';
            $entete = 'Date|Libellé|Montant|Caisse';
            $cols = 'datedepense|libelle|montantdepense|caisse';
            $btn = 'modifier|supprimer';

            session(['config' => 'depense']);
            session(['module' => 'depense']);
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
            return redirect()->route('depense')->with('error', 'Erreur de chargement : ' . $e->getMessage());
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
        $titre = ($id != null) ? "Modification d'une dépense" : "Ajout d'une dépense";
        session(['config' => 'depense']);
        session(['module' => 'depense']);
        $valider = "enregistrer-depense";

        $lenregistrement = ($id != null) ? depense::findOrFail($id) : null;
        if ($lenregistrement) {
            $this->authorizeRecordOwner($lenregistrement);
        }
        $caisses = caisse::orderBy('libelle')->get();

        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'valider',
            'caisses'
        ));
    }

    public function enregistrer(Request $request)
    {
        $id = $request->input('idenreg');
        $existing = ($id != 0 && $id != null) ? depense::findOrFail($id) : null;
        if ($existing) {
            $this->authorizeRecordOwner($existing);
        }

       // dd($request);
        try {
            if ($id == 0 || $id == null) {
                $doub = depense::where('datedepense', $request->datedepense)
                    ->where('idcaisse', $request->idcaisse)
                    ->get();
                $e = new depense();
            } else {
                $doub = depense::where('datedepense', $request->datedepense)
                    ->where('idcaisse', $request->idcaisse)
                    ->where('id', '!=', $id)->get();
                $e = $existing;
            }
            

            if (count($doub) == 0) {
                $u = Auth::user();
                $e->datedepense        = $request->datedepense;
                $e->montantdepense        = sansespace($request->montantdepense);
                $e->libelle    = $request->libelle;
                $e->idcaisse = ($request->idcaisse && $request->idcaisse != '0') ? $request->idcaisse : null;
                if ($id == 0 || $id == null) {
                    $e->iduser = $u?->id;
                }
               if($e->save()){
               
                $rk = "SELECT id FROM operationfinancieres WHERE idcaisse = '".$request->idcaisse."' AND libsrce = 'depenses' AND idsrce = '".$e->id."'";
                $idop = infoParRekete($rk, 'id');
                
                enregistreOperationFinanciere('SORTIE', sansespace($request->montantdepense), $e->id, 'depenses', $request->idcaisse, $idop);
                
                return redirect()->route('depense');
               }else{
                return redirect()->route('depense')->with('error', 'Erreur d\'enregistrement : ' . $e->getMessage());
               }
            } else {
                $info = "La dépense que vous tentez d'enregistrer existe déjà (même date, source et caisse).";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('depense')->with('error', 'Erreur d\'enregistrement : ' . $e->getMessage());
        }
    }

    public function supprimer($id)
    {
        $depense = depense::findOrFail($id);
        $this->authorizeRecordOwner($depense);

        try {
            $hasTransferts = DB::table('transferts')
                ->where('idexpediteur', $id)
                ->orWhere('idrecepteur', $id)
                ->exists();

            if (!$hasTransferts) {
                $depense->delete();
                return redirect()->route('depense');
            } else {
                $info = "Cette dépense est liée à au moins un transfert.\nVous ne pouvez pas la supprimer.";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            return redirect()->route('depense')->with('error', 'Erreur de suppression : ' . $e->getMessage());
        }
    }
}
