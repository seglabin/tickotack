<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\conteneur;
use App\Models\element;

class ConteneurController extends Controller
{
    public function index(Request $request)
    {
        try {
            $titre = 'Liste des expéditions de cargo/conteneur';
            $donnees = collect(DB::select("\n                SELECT c.id,  c.numconteneur, c.typeconteneur, c.coutcfa,
                       c.lienconteneur, e.libelle AS consignataire,
                       tn.libelle AS typenvoi, mt.libelle AS moyentransport,
                       c.datedepart, c.douane, c.poidstotal, et.libelle AS etat,
                       CASE WHEN hc.idetat IS NULL THEN '1' ELSE '0' END AS maj,
                       hc.idetat, he.libelle AS etat
                FROM conteneurs c
                LEFT JOIN elements e ON e.id = c.idconsignataire
                LEFT JOIN elements tn ON tn.id = c.idtypenvoi
                LEFT JOIN elements mt ON mt.id = c.idmoyentransport
                LEFT JOIN elements et ON et.id = c.idetat
                LEFT JOIN histoconteneurs hc ON hc.id = (
                    SELECT MAX(hc2.id)
                    FROM histoconteneurs hc2
                    WHERE hc2.ideconteneur = c.id
                )
                LEFT JOIN elements he ON he.id = hc.idetat
                ORDER BY c.id DESC
            "));
            $nouv = '/ajout-conteneur';
            $entete = 'Référence|Moyen de transport|Type d\'envoi|Type|Coût (CFA)|Consignataire|État';
            $cols = 'numconteneur|moyentransport|typenvoi|typeconteneur|coutcfa|consignataire|etat';
            $btn = 'details|modifier|supprimer|envoyer|passagedouane|arriveemagasin';

            session(['config' => 'conteneur']);
            session(['module' => 'conteneur']);

            return view('back.liste', compact('donnees', 'titre', 'nouv', 'entete', 'cols', 'btn'));
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->route('conteneur')->with('error', 'Erreur de chargement : ' . $e->getMessage());
        }
    }

    public function expeditions($id)
    {
        $conteneur = DB::table('conteneurs as c')
            ->leftJoin('elements as mt', 'mt.id', '=', 'c.idmoyentransport')
            ->leftJoin('elements as tn', 'tn.id', '=', 'c.idtypenvoi')
            ->where('c.id', $id)
            ->first([
                'c.numconteneur as reference',
                'mt.libelle as moyentransport',
                'tn.libelle as typenvoi',
            ]);

        abort_if(!$conteneur, 404);

        $expeditions = DB::table('expeditions as e')
            ->leftJoin('acteurs as exp', 'exp.id', '=', 'e.idexpediteur')
            ->leftJoin('acteurs as rec', 'rec.id', '=', 'e.idrecepteur')
            ->leftJoin('adresses as dep', 'dep.id', '=', 'e.iddepart')
            ->leftJoin('adresses as dest', 'dest.id', '=', 'e.iddestination')
            ->leftJoin('elements as et', 'et.id', '=', 'e.idetat')
            ->where('e.idconteneur', $id)
            ->orderByDesc('e.datenreg')
            ->get([
                'e.code', 'e.datenreg', 'exp.nom as expediteur', 'rec.nom as recepteur',
                'dep.libelle as villedepart', 'dest.libelle as villedestination',
                'e.nbrcolis', 'e.poids', 'et.libelle as etat',
            ]);

        return response()->json([
            'conteneur' => $conteneur,
            'expeditions' => $expeditions,
        ]);
    }

    public function formModif(Request $request, $id)
    {
        $request['idenreg'] = $id;
        return $this->form($request);
    }

    public function form(Request $request)
    {
        $id = $request->input('idenreg');
        $titre = $id ? 'Modification d\'une expédition de cargo/conteneur' : 'Ajout d\'une expédition de cargo/conteneur';
        $valider = 'enregistrer-conteneur';
        $lenregistrement = $id ? conteneur::findOrFail($id) : null;
        $consignataires = element::where('nomtable', 'consignataire')->orderBy('libelle')->get();
        $typenvois = element::where('nomtable', 'typenvoi')->orderBy('libelle')->get();
        $moyentransports = element::where('nomtable', 'moyentransport')->orderBy('libelle')->get();

        $normalTypeId = element::where('nomtable', 'typenvoi')
            ->whereRaw('LOWER(libelle) = ?', ['normal'])
            ->value('id');
        $transportId = $request->input('idmoyentransport', $lenregistrement?->idmoyentransport);
        $typeId = $request->input('idtypenvoi', $lenregistrement?->idtypenvoi);
        $transportLabel = element::whereKey($transportId)->value('libelle');

        if (str_contains(mb_strtolower((string) $transportLabel), 'bateau')) {
            $typeId = $normalTypeId;
            $request->merge(['idtypenvoi' => $normalTypeId]);
        }
        
        $expeditions = $this->chargerExpeditionsDisponibles($id, $transportId, $typeId);

        session(['config' => 'conteneur']);
        session(['module' => 'conteneur']);

        return view('back.formulaire', compact(
            'lenregistrement', 'id', 'titre', 'valider', 'consignataires',
            'typenvois', 'moyentransports', 'expeditions', 'normalTypeId'
        ));
    }

    public function expeditionsDisponibles(Request $request)
    {
        $transportId = $request->input('idmoyentransport');
        $typeId = $request->input('idtypenvoi');
        $transportLabel = element::whereKey($transportId)->value('libelle');

        if (str_contains(mb_strtolower((string) $transportLabel), 'bateau')) {
            $typeId = element::where('nomtable', 'typenvoi')
                ->whereRaw('LOWER(libelle) = ?', ['normal'])
                ->value('id');
        }

        return response()->json($this->chargerExpeditionsDisponibles(
            $request->input('idenreg'),
            $transportId,
            $typeId
        ));
    }

    private function chargerExpeditionsDisponibles($id, $transportId, $typeId)
    {
        if (!$transportId || !$typeId) {
            return collect();
        }

        return collect(DB::select(
            "SELECT e.id, e.code, e.datenreg, e.nbrcolis, e.poids, e.idconteneur,
                    exp.nom AS expediteur, rec.nom AS recepteur,
                    dep.libelle AS villedepart, dest.libelle AS villedestination,
                    et.libelle AS etat
             FROM expeditions e
             LEFT JOIN acteurs exp ON exp.id = e.idexpediteur
             LEFT JOIN acteurs rec ON rec.id = e.idrecepteur
             LEFT JOIN adresses dep ON dep.id = e.iddepart
             LEFT JOIN adresses dest ON dest.id = e.iddestination
             LEFT JOIN elements et ON et.id = e.idetat
             WHERE (LOWER(et.libelle) = 'enregistrement' OR e.idconteneur = ?)
             AND e.idmoyentransport = ? AND e.idtypenvoi = ?
             ORDER BY e.datenreg DESC",
            [$id ?: 0, $transportId, $typeId]
        ));
    }

    public function enregistrer(Request $request)
    {
        try {
            //code...
       
        $transportLabel = element::whereKey($request->input('idmoyentransport'))->value('libelle');
        $isBateau = str_contains(mb_strtolower((string) $transportLabel), 'bateau');
        $normalTypeId = element::where('nomtable', 'typenvoi')
            ->whereRaw('LOWER(libelle) = ?', ['normal'])
            ->value('id');
// dd($request->idconsignataire);
if($request->idconsignataire==0) $request['idconsignataire'] =null;
        $validated = $request->validate([
            'numconteneur' => ['required', 'string', 'max:255'],
            'typeconteneur' => [$isBateau ? 'required' : 'nullable', 'string', 'max:30'],
            // 'coutcfa' => ['required', 'numeric', 'min:0'],
            'lienconteneur' => [$isBateau ? 'required' : 'nullable', 'string', 'max:255'],
            'idconsignataire' => [$isBateau ? 'required' : 'nullable', 'integer', 'exists:elements,id'],
            'idtypenvoi' => ['required', 'integer', 'exists:elements,id'],
            'idmoyentransport' => ['required', 'integer', 'exists:elements,id'],
            'idetat' => ['nullable', 'integer', 'exists:elements,id'],
            'datedepart' => ['nullable', 'date'],
            'douane' => ['nullable', 'numeric', 'min:0'],
            'expedition_ids' => ['nullable', 'array'],
            'expedition_ids.*' => ['integer', 'exists:expeditions,id'],
        ]);

        if ($isBateau) {
            $validated['idtypenvoi'] = $normalTypeId;
        }

        $id = $request->input('idenreg');
        $doublon = conteneur::where('numconteneur', $validated['numconteneur'])
            ->when($id, fn ($query) => $query->where('id', '!=', $id))
            ->exists();

        if ($doublon) {
            return view('alertDoublon', [
                'info' => 'Ce numéro de conteneur existe déjà.',
                'titre' => 'DOUBLON',
            ]);
        }

        $conteneur = $id ? conteneur::findOrFail($id) : new conteneur();
        $conteneur->fill($validated);
        $conteneur->idetat = $validated['idetat'] ?? 1;
        $conteneur->save();

        $etatEnregistrementIds = element::where('nomtable', 'etat')
            ->whereRaw('LOWER(libelle) = ?', ['enregistrement'])
            ->pluck('id');
        $expeditionIds = $validated['expedition_ids'] ?? [];

        $expeditionsSelectionnees = DB::table('expeditions')
            ->whereIn('id', $expeditionIds)
            ->where(function ($query) use ($etatEnregistrementIds, $id, $conteneur) {
                $query->whereIn('idetat', $etatEnregistrementIds)
                    ->orWhere('idconteneur', $id ?: $conteneur->id);
            })
            ->get(['poids']);
        $conteneur->poidstotal = $expeditionsSelectionnees->sum('poids');
        $conteneur->save();

        if ($expeditionIds) {
            $etatEnvoye = element::firstOrCreate([
                'nomtable' => 'etat',
                'libelle' => 'Envoyé',
            ]);

            DB::table('expeditions')
                ->whereIn('id', $expeditionIds)
                ->where(function ($query) use ($etatEnregistrementIds, $id, $conteneur) {
                    $query->whereIn('idetat', $etatEnregistrementIds)
                        ->orWhere('idconteneur', $id ?: $conteneur->id);
                })
                ->update([
                    'idconteneur' => $conteneur->id,
                    'idetat' => $etatEnvoye->id,
                ]);
        }

        return redirect()->route('conteneur');
         } catch (\Exception $e) {
            dd($e);
            return redirect()->route('conteneur')->with('error', 'Erreur d\'enregistrement : ' . $e->getMessage());
        }
    }

    public function supprimer($id)
    {
        try {
            $utilise = DB::table('expeditions')->where('idconteneur', $id)->exists();
            if ($utilise) {
                return view('alertDoublon', [
                    'info' => 'Ce conteneur est lié à une expédition. Vous ne pouvez pas le supprimer.',
                    'titre' => 'CONTRAINTE D\'INTEGRITE',
                ]);
            }

            conteneur::findOrFail($id)->delete();
            return redirect()->route('conteneur');
        } catch (\Exception $e) {
            return redirect()->route('conteneur')->with('error', 'Erreur de suppression : ' . $e->getMessage());
        }
    }
}
