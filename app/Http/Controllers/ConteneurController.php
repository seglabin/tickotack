<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\conteneur;
use App\Models\element;
use App\Models\adresse;

class ConteneurController extends Controller
{
    public function index(Request $request)
    {
        try {
            $titre = 'Liste des expéditions de cargo/conteneur';
            $rekete = "\n                SELECT c.id,  c.numconteneur, c.typeconteneur, c.coutcfa,
                       c.lienconteneur, e.libelle AS consignataire, c.idetat,
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
            ";
            // dd($rekete);
            $donnees = collect(DB::select($rekete));

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
    { //dd($id);
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
                'e.code',
                'e.datenreg',
                'exp.nom as expediteur',
                'rec.nom as recepteur',
                'dep.libelle as villedepart',
                'dest.libelle as villedestination',
                'e.nbrcolis',
                'e.poids',
                'et.libelle as etat',
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
        $iddepart = $request->input('iddepart', $lenregistrement?->iddepart);
        $iddestination = $request->input('iddestination', $lenregistrement?->iddestination);
        $transportLabel = element::whereKey($transportId)->value('libelle');
        $adresses = adresse::orderBy('libelle')->get();
        if (str_contains(mb_strtolower((string) $transportLabel), 'bateau')) {
            $typeId = $normalTypeId;
            $request->merge(['idtypenvoi' => $normalTypeId]);
        }

        $expeditions = $this->chargerExpeditionsDisponibles($id, $transportId, $typeId, $iddepart, $iddestination);

        session(['config' => 'conteneur']);
        session(['module' => 'conteneur']);

        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'valider',
            'consignataires',
            'typenvois',
            'moyentransports',
            'expeditions',
            'normalTypeId',
            'adresses'
        ));
    }

    public function expeditionsDisponibles(Request $request)
    { //dd($request->all());
        $transportId = $request->input('idmoyentransport');
        $typeId = $request->input('idtypenvoi');
        $iddepart = $request->input('iddepart');
        $iddestination = $request->input('iddestination');
        $transportLabel = element::whereKey($transportId)->value('libelle');

        if (str_contains(mb_strtolower((string) $transportLabel), 'bateau')) {
            $typeId = element::where('nomtable', 'typenvoi')
                ->whereRaw('LOWER(libelle) = ?', ['normal'])
                ->value('id');
        }

        return response()->json($this->chargerExpeditionsDisponibles(
            $request->input('idenreg'),
            $transportId,
            $typeId,
            $iddepart,
            $iddestination
        ));
    }

    private function chargerExpeditionsDisponibles($id, $transportId, $typeId, $iddepart, $iddestination)
    {
        if (!$transportId || !$typeId) {
            return collect();
        }

        $sql = "SELECT e.id, e.code, e.datenreg, e.nbrcolis, e.poids, e.idconteneur,
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
             AND e.idmoyentransport = ? AND e.idtypenvoi = ?";
        $parametres = [$id ?: 0, $transportId, $typeId];

        if (!empty($iddepart)) {
            $sql .= ' AND e.iddepart = ?';
            $parametres[] = $iddepart;
        }

        if (!empty($iddestination)) {
            $sql .= ' AND e.iddestination = ?';
            $parametres[] = $iddestination;
        }

        $sql .= ' ORDER BY e.datenreg DESC';

        return collect(DB::select($sql, $parametres));
    }

    public function enregistrer(Request $request)
    {
        try {
            //code...
            $id = $request->input('idenreg');
            $existing = ($id != 0 && $id != null) ? conteneur::findOrFail($id) : null;
            if ($existing) {
                $this->authorizeRecordOwner($existing);
            }

            $transportLabel = element::whereKey($request->input('idmoyentransport'))->value('libelle');
            $isBateau = str_contains(mb_strtolower((string) $transportLabel), 'bateau');
            $normalTypeId = element::where('nomtable', 'typenvoi')
                ->whereRaw('LOWER(libelle) = ?', ['normal'])
                ->value('id');

            $iddepart = $request->input('iddepart');
            $iddestination = $request->input('iddestination');
            if (
                $request->filled('iddepart')
                && $request->filled('iddestination')
                && (string) $iddepart !== '0'
                && (string) $iddestination !== '0'
                && (string) $iddepart === (string) $iddestination
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'iddestination' => 'La ville de départ doit être différente de la ville de destination.',
                ]);
            }

            // dd($request->idconsignataire);
            if ($request->idconsignataire == 0) $request['idconsignataire'] = null;
            $validated = $request->validate([
                'numconteneur' => ['required', 'string', 'max:255'],
                'typeconteneur' => [$isBateau ? 'required' : 'nullable', 'string', 'max:30'],
                // 'coutcfa' => ['required', 'numeric', 'min:0'],
                'lienconteneur' => [$isBateau ? 'required' : 'nullable', 'string', 'max:255'],
                'idconsignataire' => [$isBateau ? 'required' : 'nullable', 'integer', 'exists:elements,id'],
                'idtypenvoi' => ['required', 'integer', 'exists:elements,id'],
                'idmoyentransport' => ['required', 'integer', 'exists:elements,id'],
                'iddepart' => ['required', 'integer', 'exists:adresses,id'],
                'iddestination' => ['required', 'integer', 'exists:adresses,id'],
                'idetat' => ['nullable', 'integer', 'exists:elements,id'],
                'datedepart' => ['nullable', 'date'],
                'douane' => ['nullable', 'numeric', 'min:0'],
                'expedition_ids' => ['nullable', 'array'],
                'expedition_ids.*' => ['integer', 'exists:expeditions,id'],
            ]);

            if ($isBateau) {
                $validated['idtypenvoi'] = $normalTypeId;
            }


            $doublon = conteneur::where('numconteneur', $validated['numconteneur'])
                ->when($id, fn($query) => $query->where('id', '!=', $id))
                ->exists();

            if ($doublon) {
                return view('alertDoublon', [
                    'info' => 'Ce numéro de conteneur existe déjà.',
                    'titre' => 'DOUBLON',
                ]);
            }

            if ($id == 0 || $id == null) {
                $e = new conteneur();
            } else {
                $e = $existing;
            }

            $e->numconteneur = $request->numconteneur;
            $e->idtypenvoi = $request->idtypenvoi;
            $e->idmoyentransport = $request->idmoyentransport;
            $e->iddepart = $request->iddepart;
            $e->iddestination = $request->iddestination;
            $e->idetat = $request->idetat ?? 1;
            $e->datedepart = $request->datedepart;
            $e->poidstotal = $request->poidstotal;

            if ($request->typeconteneur !== null) $e->typeconteneur = $request->typeconteneur;
            if ($request->coutcfa !== null) $e->coutcfa = $request->coutcfa;
            if ($request->lienconteneur !== null) $e->lienconteneur = $request->lienconteneur;
            if ($request->idconsignataire !== null) $e->idconsignataire = $request->idconsignataire;
            if ($request->douane !== null) $e->douane = $request->douane;

            if ($id == 0 || $id == null) {
                $e->iduser = Auth::id();
            }


            DB::transaction(function () use ($e, $validated) {
                $ancienExpeditionIds = DB::table('expeditions')
                    ->where('idconteneur', $e->id)
                    ->pluck('id')
                    ->all();

                $e->idtypenvoi = $validated['idtypenvoi'];
                $e->idmoyentransport = $validated['idmoyentransport'];
                $e->iddepart = $validated['iddepart'];
                $e->iddestination = $validated['iddestination'];
              if ( $e->save()){

                  
                  $etatEnregistrement = element::where('nomtable', 'etat')
                      ->whereRaw('LOWER(libelle) = ?', ['enregistrement'])
                      ->first();
                  if (!$etatEnregistrement) {
                      $etatEnregistrement = element::create([
                          'nomtable' => 'etat',
                          'libelle' => 'Enregistrement',
                      ]);
                  }
  
                  $selectionDemandee = array_values(array_unique(array_map(
                      'intval',
                      $validated['expedition_ids'] ?? []
                  )));
                  $expeditionIdsValides = DB::table('expeditions')
                      ->whereIn('id', $selectionDemandee)
                      ->where('idmoyentransport', $validated['idmoyentransport'])
                      ->where('idtypenvoi', $validated['idtypenvoi'])
                      ->where('iddepart', $validated['iddepart'])
                      ->where('iddestination', $validated['iddestination'])
                      ->where(function ($query) use ($etatEnregistrement, $e) {
                          $query->where('idetat', $etatEnregistrement->id)
                              ->orWhere('idconteneur', $e->id);
                      })
                      ->pluck('id')
                      ->all();
  
                  $expeditionsADetacher = array_diff($ancienExpeditionIds, $expeditionIdsValides);
                  if ($expeditionsADetacher) {
                      DB::table('expeditions')
                          ->whereIn('id', $expeditionsADetacher)
                          ->update([
                              'idconteneur' => null,
                              'idetat' => $etatEnregistrement->id,
                          ]);
                  }
  
                  if ($expeditionIdsValides) {
                      $etatRattache = element::firstOrCreate([
                          'nomtable' => 'etat',
                          'libelle' => 'Rattaché',
                      ]);
  
                      DB::table('expeditions')
                          ->whereIn('id', $expeditionIdsValides)
                          ->update([
                              'idconteneur' => $e->id,
                              'idetat' => $etatRattache->id,
                          ]);
                  }
              }

                // $e->poidstotal = DB::table('expeditions')
                //     ->where('idconteneur', $e->id)
                //     ->sum('poids');
                // $e->save();
            });

            return redirect()->route('conteneur');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
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
