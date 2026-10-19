<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\conteneur;
use App\Models\element;
use App\Models\histoconteneur;

class HistoconteneurController extends Controller
{
    public function enregistrer(Request $request)
    {
        $actions = [
            'envoyer' => ['label' => 'Envoyé', 'precedent' => null],
            'passagedouane' => ['label' => 'Passage Douane', 'precedent' => 'Envoyé'],
            'arriveemagasin' => ['label' => 'Arrivé au magasin', 'precedent' => 'Passage Douane'],
        ];

        $donnees = $request->validate([
            'ideconteneur' => ['required', 'integer', 'exists:conteneurs,id'],
            'action' => ['required', 'string', 'in:' . implode(',', array_keys($actions))],
            'datetat' => ['required', 'date'],
            'observation' => ['nullable', 'string', 'max:255'],
            'douane' => ['exclude_unless:action,passagedouane', 'required', 'numeric', 'min:0'],
        ]);

        try {
            $histo = DB::transaction(function () use ($donnees, $actions) {
                conteneur::whereKey($donnees['ideconteneur'])->lockForUpdate()->firstOrFail();
                $dernierHisto = histoconteneur::where('ideconteneur', $donnees['ideconteneur'])
                    ->latest('id')
                    ->first();
                $precedent = $actions[$donnees['action']]['precedent'];

                if (($dernierHisto && $precedent === null) || ($precedent !== null && !$dernierHisto)) {
                    throw new \InvalidArgumentException('Cette étape a déjà été enregistrée ou la séquence est invalide.');
                }

                if ($precedent !== null) {
                    $etatPrecedent = element::find($dernierHisto->idetat);
                    if (!$etatPrecedent || mb_strtolower($etatPrecedent->libelle) !== mb_strtolower($precedent)) {
                        throw new \InvalidArgumentException('Cette étape ne peut pas être enregistrée dans la séquence actuelle.');
                    }
                }

                $etat = element::firstOrCreate([
                    'nomtable' => 'etat',
                    'libelle' => $actions[$donnees['action']]['label'],
                ]);

                $miseAJourConteneur = ['idetat' => $etat->id];
                if ($donnees['action'] === 'passagedouane') {
                    $miseAJourConteneur['douane'] = $donnees['douane'];
                }
                conteneur::whereKey($donnees['ideconteneur'])->update($miseAJourConteneur);

                DB::table('expeditions')
                    ->where('idconteneur', $donnees['ideconteneur'])
                    ->update([
                        'idetat' => $etat->id,
                    ]);

                return histoconteneur::create([
                    'datetat' => $donnees['datetat'],
                    'observation' => $donnees['observation'] ?? null,
                    'ideconteneur' => $donnees['ideconteneur'],
                    'idetat' => $etat->id,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Étape du conteneur enregistrée.',
                'id' => $histo->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage(),
            ], 422);
        }
    }
}
