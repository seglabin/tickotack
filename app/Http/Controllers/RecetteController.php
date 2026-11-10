<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\caisse;
use App\Models\recette;

class RecetteController extends Controller
{
    public function index()
    {
        $titre = 'Liste des recettes';
        $user = Auth::user();
        $isAdministrator = (int) ($user->idrole ?? 0) === 1;

        $query = DB::table('recettes as r')
            ->leftJoin('caisses as c', 'c.id', '=', 'r.idcaisse')
            ->select(
                'r.id',
                'r.iduser',
                'r.daterecette',
                'r.libelle',
                'r.montantrecette',
                'c.libelle as caisse'
            )
            ->selectRaw('CASE WHEN r.iduser = ? OR ? = 1 THEN 1 ELSE 0 END AS peutmodifier', [
                $user->id,
                $isAdministrator ? 1 : 0,
            ]);

        if (!$isAdministrator) {
            $query->where('r.iduser', $user->id);
        }

        $donnees = $query->orderByDesc('r.daterecette')->get();
        $nouv = '/ajout-recette';
        $entete = 'Date|Libellé|Montant|Caisse';
        $cols = 'daterecette|libelle|montantrecette|caisse';
        $btn = 'modifier|supprimer';

        session(['config' => 'recette', 'module' => 'recette']);

        return view('back.liste', compact('donnees', 'titre', 'nouv', 'entete', 'cols', 'btn'));
    }

    public function formModif(Request $request, $id)
    {
        $request->merge(['idenreg' => $id]);

        return $this->form($request);
    }

    public function form(Request $request)
    {
        $id = $request->input('idenreg');
        $lenregistrement = $id ? recette::findOrFail($id) : null;
        if ($lenregistrement) {
            $this->authorizeRecordOwner($lenregistrement);
        }

        $titre = $lenregistrement ? "Modification d'une recette" : "Ajout d'une recette";
        $valider = 'enregistrer-recette';
        $caisses = caisse::orderBy('libelle')->get();
        session(['config' => 'recette', 'module' => 'recette']);

        return view('back.formulaire', compact('lenregistrement', 'id', 'titre', 'valider', 'caisses'));
    }

    public function enregistrer(Request $request)
    {
        $request->validate(['idenreg' => ['nullable', 'integer', 'min:0']]);

        $id = (int) $request->input('idenreg', 0);
        $existing = $id > 0 ? recette::findOrFail($id) : null;
        if ($existing) {
            $this->authorizeRecordOwner($existing);
        }

        $user = Auth::user();
        $isAdministrator = (int) ($user->idrole ?? 0) === 1;
        $caisseId = $isAdministrator ? $request->input('idcaisse') : $user->idcaisse;
        $montant = str_replace([' ', "\u{00A0}"], '', (string) $request->input('montantrecette'));
        $request->merge(['idcaisse' => $caisseId, 'montantrecette' => $montant]);

        $validated = $request->validate([
            'daterecette' => ['required', 'date'],
            'libelle' => ['required', 'string', 'max:255'],
            'montantrecette' => ['required', 'numeric', 'min:0.01'],
            'idcaisse' => ['required', 'integer', Rule::exists('caisses', 'id')],
        ]);

        $duplicate = recette::where('daterecette', $validated['daterecette'])
            ->where('idcaisse', $validated['idcaisse'])
            ->when($existing, fn ($query) => $query->where('id', '!=', $existing->id))
            ->exists();
        if ($duplicate) {
            $info = 'Une recette enregistrée à cette date dans cette caisse existe déjà.';
            $titre = 'DOUBLON';

            return view('alertDoublon', compact('info', 'titre'));
        }

        DB::transaction(function () use ($existing, $validated, $user): void {
            $record = $existing ?? new recette();
            $oldCashboxId = $record->idcaisse;
            $record->daterecette = $validated['daterecette'];
            $record->libelle = $validated['libelle'];
            $record->montantrecette = $validated['montantrecette'];
            $record->idcaisse = $validated['idcaisse'];
            if (!$existing) {
                $record->iduser = $user->id;
            }
            $record->saveOrFail();

            $operation = DB::table('operationfinancieres')
                ->where('libsrce', 'recettes')
                ->where('idsrce', $record->id)
                ->first();
            $operationData = [
                'sens' => 'Entree',
                'montant' => $record->montantrecette,
                'idsrce' => $record->id,
                'libsrce' => 'recettes',
                'idcaisse' => $record->idcaisse,
                'updated_at' => now(),
            ];
            if ($operation) {
                DB::table('operationfinancieres')->where('id', $operation->id)->update($operationData);
            } else {
                $operationData['created_at'] = now();
                DB::table('operationfinancieres')->insert($operationData);
            }

            $cashboxIds = collect([$oldCashboxId, $record->idcaisse])->filter()->unique();
            foreach ($cashboxIds as $cashboxId) {
                $income = DB::table('operationfinancieres')
                    ->where('idcaisse', $cashboxId)
                    ->where('sens', 'Entree')
                    ->sum('montant');
                $expense = DB::table('operationfinancieres')
                    ->where('idcaisse', $cashboxId)
                    ->where('sens', 'Sortie')
                    ->sum('montant');

                DB::table('caisses')->where('id', $cashboxId)->update([
                    'montant' => $income - $expense,
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('recette');
    }

    public function supprimer($id)
    {
        $record = recette::findOrFail($id);
        $this->authorizeRecordOwner($record);

        DB::transaction(function () use ($record): void {
            DB::table('operationfinancieres')
                ->where('libsrce', 'recettes')
                ->where('idsrce', $record->id)
                ->delete();
            $record->delete();

            $income = DB::table('operationfinancieres')
                ->where('idcaisse', $record->idcaisse)
                ->where('sens', 'Entree')
                ->sum('montant');
            $expense = DB::table('operationfinancieres')
                ->where('idcaisse', $record->idcaisse)
                ->where('sens', 'Sortie')
                ->sum('montant');
            DB::table('caisses')->where('id', $record->idcaisse)->update([
                'montant' => $income - $expense,
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('recette');
    }
}
