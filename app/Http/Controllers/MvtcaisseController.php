<?php

namespace App\Http\Controllers;

use App\Models\caisse;
use App\Models\mvtcaisse;
use App\Models\operationfinanciere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MvtcaisseController extends Controller
{
    public function index()
    {
        try {
            $titre = 'Liste des transactions de caisses';
            $utilisateur = Auth::user();
            $admin = $utilisateur->idrole == 1;
            $maj = $admin ? '1' : 'CASE WHEN m.iduser = ? THEN 1 ELSE 0 END';
            $filtreCaisse = $admin ? '' : 'WHERE m.idcaissesortie = ? OR m.idcaissentree = ?';
            $parametres = $admin
                ? []
                : [Auth::id(), $utilisateur->idcaisse, $utilisateur->idcaisse];

            $donnees = collect(DB::select("
                SELECT m.id, m.iduser, $maj AS maj, m.datemvt, m.montantsortie, m.montantentree,
                    cs.libelle AS caissesortie, ce.libelle AS caissentree, m.observation
                FROM mvtcaisses m
                LEFT JOIN caisses cs ON cs.id = m.idcaissesortie
                LEFT JOIN caisses ce ON ce.id = m.idcaissentree
                $filtreCaisse
                ORDER BY m.datemvt DESC
            ", $parametres));
            $nouv = '/ajout-mvtcaisse';
            $entete = 'Date|Montant sortie|Caisse sortie|Montant entrée|Caisse entrée|Observation';
            $cols = 'datemvt|montantsortie|caissesortie|montantentree|caissentree|observation';
            $btn = 'modifier|supprimer';

            session(['config' => 'mvtcaisse']);
            session(['module' => 'mvtcaisse']);

            return view('back.liste', compact('donnees', 'titre', 'nouv', 'entete', 'cols', 'btn'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('mvtcaisse')->with('error', 'Erreur de chargement : ' . $e->getMessage());
        }
    }

    public function formModif(Request $request, $id)
    {
        $request->merge(['idenreg' => $id]);

        return $this->form($request);
    }

    public function form(Request $request)
    {
        $id = $request->input('idenreg');
        $titre = $id ? "Modification d'une transaction de caisses" : "Ajout d'une transaction de caisses";
        $valider = 'enregistrer-mvtcaisse';
        $lenregistrement = $id ? mvtcaisse::findOrFail($id) : null;
        if ($lenregistrement) {
            $this->ensureCanManage($lenregistrement);
        }
        $caisses = caisse::orderBy('libelle')->get();
        $utilisateur = Auth::user();
        $admin = $utilisateur->idrole == 1;
        $caisseSortieRattachee = $utilisateur->idcaisse
            ? caisse::find($utilisateur->idcaisse)
            : null;

        session(['config' => 'mvtcaisse']);
        session(['module' => 'mvtcaisse']);

        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'valider',
            'caisses',
            'admin',
            'caisseSortieRattachee'
        ));
    }

    public function enregistrer(Request $request)
    {
        $request->merge([
            'montantsortie' => sansespace((string) $request->input('montantsortie', '')),
            'montantentree' => sansespace((string) $request->input('montantentree', '')),
        ]);
        if (Auth::user()->idrole != 1) {
            $request->merge(['idcaissesortie' => Auth::user()->idcaisse]);
        }

        $validated = $request->validate([
            'idenreg' => ['nullable', 'integer'],
            'datemvt' => ['required', 'date'],
            'montantsortie' => ['required', 'numeric', 'min:0.01'],
            'montantentree' => ['required', 'numeric', 'min:0.01'],
            'idcaissesortie' => ['required', 'exists:caisses,id', 'different:idcaissentree'],
            'idcaissentree' => ['required', 'exists:caisses,id'],
            'observation' => ['nullable', 'string'],
        ], [
            'idcaissesortie.required' => 'Aucune caisse de sortie n’est rattachée à votre profil.',
            'idcaissesortie.different' => 'Les caisses de sortie et d’entrée doivent être différentes.',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $id = $validated['idenreg'] ?? null;
                $mouvement = $id ? mvtcaisse::findOrFail($id) : new mvtcaisse();
                if ($id) {
                    $this->ensureCanManage($mouvement);
                }
                $anciennesCaisses = $id
                    ? [$mouvement->idcaissesortie, $mouvement->idcaissentree]
                    : [];

                $mouvement->datemvt = $validated['datemvt'];
                $mouvement->montantsortie = $validated['montantsortie'];
                $mouvement->montantentree = $validated['montantentree'];
                $mouvement->observation = $validated['observation'] ?? null;
                $mouvement->idcaissesortie = $validated['idcaissesortie'];
                $mouvement->idcaissentree = $validated['idcaissentree'];
                if (!$id) {
                    $mouvement->iduser = Auth::id();
                }
                $mouvement->save();

                $operations = [
                    ['sens' => 'Sortie', 'montant' => $mouvement->montantsortie, 'idcaisse' => $mouvement->idcaissesortie],
                    ['sens' => 'Entree', 'montant' => $mouvement->montantentree, 'idcaisse' => $mouvement->idcaissentree],
                ];

                foreach ($operations as $operationData) {
                    $operation = operationfinanciere::where('idsrce', $mouvement->id)
                        ->where('libsrce', 'mvtcaisses')
                        ->where('sens', $operationData['sens'])
                        ->first() ?? new operationfinanciere();

                    $operation->sens = $operationData['sens'];
                    $operation->montant = $operationData['montant'];
                    $operation->idsrce = $mouvement->id;
                    $operation->libsrce = 'mvtcaisses';
                    $operation->idcaisse = $operationData['idcaisse'];
                    $operation->save();

                    operationfinanciere::where('idsrce', $mouvement->id)
                        ->where('libsrce', 'mvtcaisses')
                        ->where('sens', $operationData['sens'])
                        ->where('id', '!=', $operation->id)
                        ->delete();
                }

                $this->actualiserSoldesCaisses(array_merge($anciennesCaisses, [
                    $mouvement->idcaissesortie,
                    $mouvement->idcaissentree,
                ]));
            });

            return redirect()->route('mvtcaisse');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->route('mvtcaisse')->with('error', 'Erreur d’enregistrement : ' . $e->getMessage());
        }
    }

    public function supprimer($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $mouvement = mvtcaisse::findOrFail($id);
                $this->ensureCanManage($mouvement);
                $caisses = [$mouvement->idcaissesortie, $mouvement->idcaissentree];

                operationfinanciere::where('idsrce', $mouvement->id)
                    ->where('libsrce', 'mvtcaisses')
                    ->delete();
                $mouvement->delete();

                $this->actualiserSoldesCaisses($caisses);
            });

            return redirect()->route('mvtcaisse');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->route('mvtcaisse')->with('error', 'Erreur de suppression : ' . $e->getMessage());
        }
    }

    private function ensureCanManage(mvtcaisse $mouvement): void
    {
        abort_unless(
            Auth::user()->idrole == 1 || $mouvement->iduser == Auth::id(),
            403
        );
    }

    private function actualiserSoldesCaisses(array $ids)
    {
        foreach (array_unique($ids) as $id) {
            $entrees = DB::table('operationfinancieres')
                ->where('idcaisse', $id)
                ->where('sens', 'Entree')
                ->sum('montant');
            $sorties = DB::table('operationfinancieres')
                ->where('idcaisse', $id)
                ->where('sens', 'Sortie')
                ->sum('montant');

            caisse::whereKey($id)->update(['montant' => $entrees - $sorties]);
        }
    }
}
