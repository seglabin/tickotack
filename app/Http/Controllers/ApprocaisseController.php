<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\approcaisse;
use App\Models\element;
use App\Models\caisse;
use Illuminate\Support\Facades\Auth;


class ApprocaisseController extends Controller
{
        public function index(Request $request)
    {
        try {
            $titre = "Liste des approvisionnements des caisses";
            $isAdmin = (int) (Auth::user()->idrole ?? 0) === 1;
            $donnees = collect(DB::select("
            SELECT a.id, a.iduser, CASE WHEN ? = 1 OR a.iduser = ? THEN 1 ELSE 0 END AS peutmodifier,
                   a.dateappro, a.montant, a.observation, s.libelle as source, c.libelle as caisse
            FROM approcaisses a
            LEFT JOIN elements s ON s.id = a.idsource
            LEFT JOIN caisses c ON c.id = a.idcaisse
            ORDER BY a.dateappro desc
            ", [$isAdmin ? 1 : 0, Auth::id()]));
            
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

        $lenregistrement = ($id != null) ? approcaisse::findOrFail($id) : null;
        if ($lenregistrement) {
            $this->authorizeRecordOwner($lenregistrement);
        }
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
        $id = $request->input('idenreg');
        $existing = ($id != 0 && $id != null) ? approcaisse::findOrFail($id) : null;
        if ($existing) {
            $this->authorizeRecordOwner($existing);
        }

       // dd($request);
        try {
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
                $e = $existing;
            }
            

            if (count($doub) == 0) {
                $e->dateappro        = $request->dateappro;
                $e->montant        = sansespace($request->montant);
                $e->observation    = $request->observation;
                $e->idsource     = $request->idsource;
                $e->idcaisse = ($request->idcaisse && $request->idcaisse != '0') ? $request->idcaisse : null;
                if ($id == 0 || $id == null) {
                    $e->iduser = Auth::id();
                }
               if($e->save()){
               
                $rk = "SELECT id FROM operationfinancieres WHERE idcaisse = '".$request->idcaisse."' AND libsrce = 'approcaisses' AND idsrce = '".$e->id."'";
                $idop = infoParRekete($rk, 'id');
                
                enregistreOperationFinanciere('Entree', sansespace($request->montant), $e->id, 'approcaisses', $request->idcaisse, $idop);
                
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
        $approvisionnement = approcaisse::findOrFail($id);
        $this->authorizeRecordOwner($approvisionnement);

        try {
            DB::transaction(function () use ($id) {
                $approvisionnement = approcaisse::whereKey($id)
                    ->lockForUpdate()
                    ->firstOrFail();
                $caisseIds = DB::table('operationfinancieres')
                    ->where('libsrce', 'approcaisses')
                    ->where('idsrce', $approvisionnement->id)
                    ->pluck('idcaisse')
                    ->push($approvisionnement->idcaisse)
                    ->map(static fn ($caisseId) => (int) $caisseId)
                    ->unique()
                    ->sort()
                    ->values();

                foreach ($caisseIds as $caisseId) {
                    caisse::whereKey($caisseId)->lockForUpdate()->firstOrFail();
                }

                DB::table('operationfinancieres')
                    ->where('libsrce', 'approcaisses')
                    ->where('idsrce', $approvisionnement->id)
                    ->delete();

                if (! $approvisionnement->delete()) {
                    throw new \RuntimeException('La suppression de l’approvisionnement a échoué.');
                }

                foreach ($caisseIds as $caisseId) {
                    DB::table('caisses')
                        ->where('id', $caisseId)
                        ->update([
                            'montant' => soldeCaisse($caisseId),
                            'updated_at' => now(),
                        ]);
                }
            });

            return redirect()->route('approcaisse')->with(
                'success',
                'Approvisionnement supprimé et solde de caisse régularisé.'
            );
        } catch (QueryException $e) {
            report($e);

            return redirect()->route('approcaisse')->with(
                'error',
                'Suppression impossible : les écritures financières n’ont pas pu être régularisées. Aucune modification n’a été conservée.'
            );
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('approcaisse')->with(
                'error',
                'Erreur de suppression : aucune modification financière n’a été conservée.'
            );
        }
    }
}
