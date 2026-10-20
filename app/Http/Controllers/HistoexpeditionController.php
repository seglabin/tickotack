<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\element;
use App\Models\expedition;
use App\Models\histoexpedition;

class HistoexpeditionController extends Controller
{
    public function historique($id)
    {
        $expedition = DB::table('expeditions as e')
            ->leftJoin('conteneurs as c', 'c.id', '=', 'e.idconteneur')
            ->where('e.id', $id)
            ->first(['e.id', 'e.code', 'e.idconteneur', 'c.numconteneur', 'c.created_at as dateintegration']);

        abort_if(!$expedition, 404, 'Colis introuvable.');

        $historiqueColis = DB::table('histoexpeditions as h')
            ->leftJoin('elements as et', 'et.id', '=', 'h.idetat')
            ->where('h.idexpedition', $id)
            ->orderBy('h.datetat')
            ->orderBy('h.id')
            ->get(['h.datetat', 'h.reference', 'et.libelle as etat'])
            ->map(fn ($etape) => [
                'datetat' => $etape->datetat,
                'origine' => 'Colis',
                'etat' => $etape->etat,
                'reference' => $etape->reference,
            ]);

        $historiqueConteneur = collect();
        if ($expedition->idconteneur) {
            $historiqueConteneur = DB::table('histoconteneurs as h')
                ->leftJoin('elements as et', 'et.id', '=', 'h.idetat')
                ->where('h.ideconteneur', $expedition->idconteneur)
                ->orderBy('h.datetat')
                ->orderBy('h.id')
                ->get(['h.datetat', 'h.observation', 'et.libelle as etat'])
                ->map(fn ($etape) => [
                    'datetat' => $etape->datetat,
                    'origine' => 'Conteneur ' . $expedition->numconteneur,
                    'etat' => $etape->etat,
                    'reference' => $etape->observation,
                ]);

            $historiqueConteneur->push([
                'datetat' => $expedition->dateintegration,
                'origine' => 'Intégration',
                'etat' => 'Intégration à l’expédition',
                'reference' => $expedition->numconteneur,
            ]);
        }

        $historique = $historiqueColis
            ->concat($historiqueConteneur)
            ->sortBy('datetat')
            ->values();

        return response()->json([
            'expedition' => $expedition,
            'historique' => $historique,
        ]);
    }

    public function recuRetrait($id)
    {
        $expedition = DB::table('expeditions as e')
            ->leftJoin('acteurs as exp', 'exp.id', '=', 'e.idexpediteur')
            ->leftJoin('acteurs as rec', 'rec.id', '=', 'e.idrecepteur')
            ->leftJoin('adresses as dep', 'dep.id', '=', 'e.iddepart')
            ->leftJoin('adresses as dest', 'dest.id', '=', 'e.iddestination')
            ->leftJoin('elements as tc', 'tc.id', '=', 'e.idtypcolis')
            ->where('e.id', $id)
            ->first([
                'e.id', 'e.code', 'e.datenreg', 'e.nbrcolis', 'e.poids', 'e.montant', 'e.montantpaye', 'e.marque',
                'exp.nom as expediteur', 'exp.tel as telephone_expediteur',
                'rec.nom as recepteur', 'rec.tel as telephone_recepteur', 'rec.carte as carte_recepteur',
                'dep.libelle as villedepart', 'dest.libelle as villedestination',
                'tc.libelle as typecolis',
            ]);

        abort_if(!$expedition, 404, 'Colis introuvable.');

        $retrait = DB::table('histoexpeditions as h')
            ->leftJoin('elements as et', 'et.id', '=', 'h.idetat')
            ->where('h.idexpedition', $id)
            ->where('et.libelle', 'Retiré')
            ->orderByDesc('h.datetat')
            ->orderByDesc('h.id')
            ->first(['h.datetat', 'h.reference']);

        abort_if(!$retrait, 404, 'Retrait introuvable.');

        $agent = auth()->user()?->name;

        return Pdf::loadView('back.expedition-recu', compact('expedition', 'retrait', 'agent'))
            ->setPaper('A5', 'portrait')
            ->stream('recu-retrait-colis-' . $expedition->code . '.pdf');
    }

    public function enregistrer(Request $request)
    { //dd($request);
        $actions = [
            'retraitcolis' => 'Retiré',
            'envoicolis' => 'Envoyé',
            'passagedouane' => 'Passage Douane',
            'arrivemagasin' => 'Arrivé au magasin',
            //'retraitmagasin' => 'Retrait magasin',
        ];

        $donnees = $request->validate([
            'idexpedition' => ['required', 'integer', 'exists:expeditions,id'],
            'action' => ['required', 'string', 'in:' . implode(',', array_keys($actions))],
            'datetat' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'idconteneur' => ['exclude_unless:action,envoicolis', 'required', 'integer', 'exists:conteneurs,id'],
            'montantpaye' => ['exclude_unless:action,retraitcolis', 'required', 'numeric', 'min:0', 'decimal:0,2'],
        ]);

        try {
            $resultat = DB::transaction(function () use ($donnees, $actions) {
                if ($donnees['action'] === 'retraitcolis') {
                    $expedition = expedition::whereKey($donnees['idexpedition'])
                        ->lockForUpdate()
                        ->firstOrFail();
                    $totalPaiements = (float) DB::table('paymentcolis')
                        ->where('idcolis', $expedition->id)
                        ->sum('montant');
                    $solde = max(0, (float) $expedition->montant - $totalPaiements);

                    if ((float) $donnees['montantpaye'] > $solde + 0.000001) {
                        return [
                            'success' => false,
                            'status' => 422,
                            'message' => 'Le montant payé au retrait ne peut pas dépasser le solde du colis (' . number_format($solde, 2, ',', ' ') . ' CFA).',
                        ];
                    }
                }

                $etat = element::firstOrCreate([
                    'nomtable' => 'etat',
                    'libelle' => $actions[$donnees['action']],
                ]);

                $histo = new histoexpedition();
                $histo->datetat = $donnees['datetat'];
                $histo->reference = $donnees['reference'] ?? null;
                $histo->idexpedition = $donnees['idexpedition'];
                $histo->idetat = $etat->id;
                $histo->save();

                expedition::whereKey($donnees['idexpedition'])->update(['idetat' => $etat->id]);
                if ($donnees['action'] === 'envoicolis') {
                    expedition::whereKey($donnees['idexpedition'])->update([
                        'idconteneur' => $donnees['idconteneur'],
                    ]);
                }
                if ($donnees['action'] === 'retraitcolis') {
                    expedition::whereKey($donnees['idexpedition'])->update([
                        'montantpaye' => $donnees['montantpaye'],
                    ]);
                }

                return ['success' => true, 'histo' => $histo];
            });

            if (!$resultat['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultat['message'],
                ], $resultat['status']);
            }

            $histo = $resultat['histo'];

            return response()->json([
                'success' => true,
                'message' => 'Étape de l\'expédition enregistrée.',
                'id' => $histo->id,
                'receipt_url' => $donnees['action'] === 'retraitcolis'
                    ? route('expedition.recu-retrait', $donnees['idexpedition'])
                    : null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage(),
            ], 500);
        }
    }
}
