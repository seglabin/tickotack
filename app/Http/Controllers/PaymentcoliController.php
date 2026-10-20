<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentcoliController extends Controller
{
    public function historique($id)
    {
        abort_unless(DB::table('expeditions')->where('id', $id)->exists(), 404, 'Colis introuvable.');

        $paiements = DB::table('paymentcolis')
            ->where('idcolis', $id)
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get(['payment_date', 'montant']);

        return response()->json([
            'paiements' => $paiements,
            'total' => (float) $paiements->sum('montant'),
        ]);
    }

    public function enregistrer(Request $request)
    {
        $donnees = $request->validate([
            'idcolis' => ['required', 'integer', 'exists:expeditions,id'],
            'montant' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'payment_date' => ['required', 'date'],
        ]);

        $resultat = DB::transaction(function () use ($donnees) {
            $expedition = DB::table('expeditions as e')
                ->leftJoin('elements as et', 'et.id', '=', 'e.idetat')
                ->where('e.id', $donnees['idcolis'])
                ->lockForUpdate()
                ->first(['e.id', 'e.montant', 'et.libelle as etat']);

            abort_unless($expedition, 404, 'Colis introuvable.');

            if (mb_strtolower(trim((string) $expedition->etat)) === 'retiré') {
                return [
                    'success' => false,
                    'status' => 422,
                    'message' => 'Le paiement ne peut pas être enregistré : ce colis a déjà été retiré.',
                ];
            }

            $totalPaye = (float) DB::table('paymentcolis')
                ->where('idcolis', $expedition->id)
                ->sum('montant');
            $montant = (float) $donnees['montant'];
            $montantExpedition = (float) $expedition->montant;

            if (round($totalPaye + $montant, 2) > round($montantExpedition, 2)) {
                return [
                    'success' => false,
                    'status' => 422,
                    'message' => 'Le montant dépasse le solde du colis (' . number_format(max(0, $montantExpedition - $totalPaye), 2, ',', ' ') . ' CFA).',
                ];
            }

            DB::table('paymentcolis')->insert([
                'idcolis' => $expedition->id,
                'montant' => $montant,
                'payment_date' => \Illuminate\Support\Carbon::parse($donnees['payment_date'])->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'success' => true,
                'total' => $totalPaye + $montant,
            ];
        });

        if (!$resultat['success']) {
            return response()->json($resultat, $resultat['status']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Paiement du colis enregistré.',
            'total' => $resultat['total'],
        ]);
    }
}
