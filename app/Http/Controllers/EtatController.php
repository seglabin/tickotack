<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EtatController extends Controller
{
    public function colis(Request $request)
    {
        $filters = $this->validatedFilters($request, ['conteneur', 'statut']);
        $query = DB::table('expeditions as e')
            ->leftJoin('acteurs as exp', 'exp.id', '=', 'e.idexpediteur')
            ->leftJoin('acteurs as rec', 'rec.id', '=', 'e.idrecepteur')
            ->leftJoin('adresses as dep', 'dep.id', '=', 'e.iddepart')
            ->leftJoin('adresses as dest', 'dest.id', '=', 'e.iddestination')
            ->leftJoin('elements as et', 'et.id', '=', 'e.idetat')
            ->leftJoin('conteneurs as co', 'co.id', '=', 'e.idconteneur')
            ->leftJoinSub(
                DB::table('paymentcolis')
                    ->select('idcolis')
                    ->selectRaw('SUM(montant) as totalpaye')
                    ->groupBy('idcolis'),
                'paiements',
                'paiements.idcolis',
                '=',
                'e.id'
            )
            ->select(
                'e.id',
                'e.code',
                'exp.nom as expediteur',
                'rec.nom as recepteur',
                'rec.tel as numrecepteur',
                'dep.libelle as villedepart',
                'dest.libelle as villedestination',
                'et.libelle as statut',
                'co.numconteneur as conteneur',
                'e.montant',
                DB::raw('COALESCE(paiements.totalpaye, 0) + COALESCE(e.montantpaye, 0) as totalpaye')
            );

        $this->restrictExpeditions($query);
        $this->applyPeriod($query, 'e.datenreg', $filters);
        if (! empty($filters['conteneur'])) {
            $query->where('e.idconteneur', $filters['conteneur']);
        }
        if (! empty($filters['statut'])) {
            $query->where('e.idetat', $filters['statut']);
        }

        $donnees = $query->orderByDesc('e.datenreg')->get();
        $conteneurs = DB::table('conteneurs')->orderBy('numconteneur')->get(['id', 'numconteneur']);
        $statuts = DB::table('elements')->where('nomtable', 'etat')->orderBy('libelle')->get(['id', 'libelle']);

        return $this->renderReport('colis', 'Liste des colis (expéditions)', $donnees, $filters, compact('conteneurs', 'statuts'));
    }

    public function paiements(Request $request)
    {
        $filters = $this->validatedFilters($request, ['conteneur', 'colis']);
        $query = DB::table('paymentcolis as p')
            ->join('expeditions as e', 'e.id', '=', 'p.idcolis')
            ->leftJoin('conteneurs as co', 'co.id', '=', 'e.idconteneur')
            ->select('p.id', 'p.payment_date', 'p.montant', 'p.idcolis', 'e.code', 'co.numconteneur');

        $this->restrictExpeditions($query);
        $this->applyPeriod($query, 'p.payment_date', $filters);
        if (! empty($filters['conteneur'])) {
            $query->where('e.idconteneur', $filters['conteneur']);
        }
        if (! empty($filters['colis'])) {
            $query->where('e.id', $filters['colis']);
        }

        $donnees = $query->orderByDesc('p.payment_date')->orderByDesc('p.id')->get();
        $conteneurs = DB::table('conteneurs')->orderBy('numconteneur')->get(['id', 'numconteneur']);
        $colis = $this->accessibleExpeditions()->orderBy('e.code')->get(['e.id', 'e.code']);

        return $this->renderReport('paiements', 'Liste des paiements', $donnees, $filters, compact('conteneurs', 'colis'));
    }

    public function transferts(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $query = DB::table('transferts as t')
            ->leftJoin('acteurs as exp', 'exp.id', '=', 't.idexpediteur')
            ->leftJoin('adresses as dep', 'dep.id', '=', 't.iddepart')
            ->leftJoin('adresses as dest', 'dest.id', '=', 't.iddestination')
            ->select(
                't.id',
                't.code',
                't.datetransfert',
                't.montdepot',
                't.montretrait',
                'exp.nom as expediteur',
                'dep.libelle as villedepart',
                'dest.libelle as villedestination'
            );

        $this->restrictTransferts($query);
        $this->applyPeriod($query, 't.datetransfert', $filters);
        $donnees = $query->orderByDesc('t.datetransfert')->get();

        return $this->renderReport('transferts', 'Liste des transferts', $donnees, $filters);
    }

    public function depenses(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $query = DB::table('depenses as d')
            ->leftJoin('caisses as c', 'c.id', '=', 'd.idcaisse')
            ->select('d.id', 'd.datedepense as date', 'd.libelle', 'd.montantdepense as montant', 'c.libelle as caisse');

        if (! $this->isAdministrator()) {
            $query->where('d.iduser', Auth::id());
        }
        $this->applyPeriod($query, 'd.datedepense', $filters);
        $donnees = $query->orderByDesc('d.datedepense')->get();

        return $this->renderReport('depenses', 'Liste des dépenses', $donnees, $filters);
    }

    public function recettes(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $query = DB::table('recettes as r')
            ->leftJoin('caisses as c', 'c.id', '=', 'r.idcaisse')
            ->select('r.id', 'r.daterecette as date', 'r.libelle', 'r.montantrecette as montant', 'c.libelle as caisse');

        if (! $this->isAdministrator()) {
            $query->where('r.iduser', Auth::id());
        }
        $this->applyPeriod($query, 'r.daterecette', $filters);
        $donnees = $query->orderByDesc('r.daterecette')->get();

        return $this->renderReport('recettes', 'Liste des autres recettes', $donnees, $filters);
    }

    private function validatedFilters(Request $request, array $extra = []): array
    {
        $rules = [
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
        ];
        if (in_array('conteneur', $extra, true)) {
            $rules['conteneur'] = ['nullable', 'integer', 'exists:conteneurs,id'];
        }
        if (in_array('statut', $extra, true)) {
            $rules['statut'] = ['nullable', 'integer', Rule::exists('elements', 'id')->where('nomtable', 'etat')];
        }
        if (in_array('colis', $extra, true)) {
            $rules['colis'] = ['nullable', 'integer', 'exists:expeditions,id'];
        }

        return $request->validate($rules);
    }

    private function applyPeriod(Builder $query, string $column, array $filters): void
    {
        if (! empty($filters['date_debut'])) {
            $query->where($column, '>=', $filters['date_debut'].' 00:00:00');
        }
        if (! empty($filters['date_fin'])) {
            $query->where($column, '<=', $filters['date_fin'].' 23:59:59');
        }
    }

    private function restrictExpeditions(Builder $query): void
    {
        if (! $this->isAdministrator()) {
            $addressId = idadresseCaisseUser();
            $query->where(function (Builder $query) use ($addressId): void {
                $query->where('e.iddepart', $addressId)
                    ->orWhere('e.iddestination', $addressId);
            });
        }
    }

    private function restrictTransferts(Builder $query): void
    {
        if (! $this->isAdministrator()) {
            $addressId = idadresseCaisseUser();
            $query->where(function (Builder $query) use ($addressId): void {
                $query->where('t.iddepart', $addressId)
                    ->orWhere('t.iddestination', $addressId);
            });
        }
    }

    private function accessibleExpeditions(): Builder
    {
        $query = DB::table('expeditions as e')->select('e.id', 'e.code');
        $this->restrictExpeditions($query);

        return $query;
    }

    private function isAdministrator(): bool
    {
        return (int) (Auth::user()->idrole ?? 0) === 1;
    }

    private function renderReport(string $type, string $titre, $donnees, array $filters, array $options = [])
    {
        session(['config' => 'etats', 'module' => 'etats-'.$type]);

        return view('back.etats', array_merge(compact('type', 'titre', 'donnees', 'filters'), $options));
    }
}
