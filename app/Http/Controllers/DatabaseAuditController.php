<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DatabaseAuditController extends Controller
{
    public function index(Request $request)
    {
        abort_unless((int) (Auth::user()->idrole ?? 0) === 1, 403);

        $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'table' => ['nullable', 'string', 'max:191'],
            'action' => ['nullable', 'in:INSERT,UPDATE,DELETE,REPLACE'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
        ]);

        $logs = DB::table('database_audit_logs as logs')
            ->leftJoin('users', 'users.id', '=', 'logs.user_id')
            ->when($request->filled('user_id'), fn ($query) => $query->where('logs.user_id', $request->input('user_id')))
            ->when($request->filled('table'), fn ($query) => $query->where('logs.table_name', $request->input('table')))
            ->when($request->filled('action'), fn ($query) => $query->where('logs.action', $request->input('action')))
            ->when($request->filled('date_debut'), fn ($query) => $query->whereDate('logs.created_at', '>=', $request->input('date_debut')))
            ->when($request->filled('date_fin'), fn ($query) => $query->whereDate('logs.created_at', '<=', $request->input('date_fin')))
            ->wherenotin('logs.table_name', ['database_audit_logs','sessions']) // Exclude logs from the audit table itself
            ->orderByDesc('logs.id')
            ->select([
                'logs.*',
                'users.name as user_name',
                'users.username as username',
            ])
            ->paginate(50)
            ->withQueryString();

        $logs->getCollection()->transform(function ($log) {
            $log->info = $this->summarizeAction($log);

            return $log;
        });

        $users = DB::table('users')->orderBy('name')->get(['id', 'name', 'username']);
        $tables = DB::table('database_audit_logs')->distinct()->orderBy('table_name')->pluck('table_name');

        return view('back.historique-actions', compact('logs', 'users', 'tables'));
    }

    private function summarizeAction(object $log): string
    {
        $tableNames = [
            'transferts' => 'transfert',
            'caisses' => 'caisse',
            'depenses' => 'dépense',
            'paymentcolis' => 'paiement de colis',
            'approcaisses' => 'approvisionnement de caisse',
            'expeditions' => 'expédition',
            'conteneurs' => 'conteneur',
            'tarifexpeditions' => 'tarif d’expédition',
            'tauxtransferts' => 'taux de transfert',
        ];
        $tableName = $tableNames[$log->table_name] ?? str_replace('_', ' ', $log->table_name);
        $tableDescription = match ($log->table_name) {
            'caisses' => 'de caisse',
            'depenses' => 'de dépense',
            'paymentcolis' => 'de paiement de colis',
            'approcaisses' => 'd’approvisionnement de caisse',
            'expeditions' => 'd’expédition',
            'conteneurs' => 'de conteneur',
            'tarifexpeditions' => 'de tarif d’expédition',
            'tauxtransferts' => 'de taux de transfert',
            default => 'de ' . $tableName,
        };
        $actionNames = [
            'INSERT' => 'Ajout',
            'UPDATE' => 'Modification',
            'DELETE' => 'Suppression',
            'REPLACE' => 'Remplacement',
        ];
        $actionName = $actionNames[$log->action] ?? ucfirst(strtolower($log->action));
        $record = $log->record_id === null ? '' : ' ID ' . $log->record_id;

        if ($log->action !== 'UPDATE') {
            $article = match ($log->table_name) {
                'caisses', 'depenses' => 'de la ',
                'expeditions' => 'de l’',
                default => 'du ',
            };

            return "{$actionName} {$article}{$tableName}{$record}";
        }

        $changes = $log->changes ? json_decode($log->changes, true) : [];
        if (!is_array($changes) || $changes === []) {
            return "{$actionName} {$tableDescription}{$record}";
        }

        $fieldNames = [
            'montant' => 'montant',
            'montantcaisse' => 'montant de caisse',
            'montantdepense' => 'montant de dépense',
            'montdepot' => 'montant du dépôt',
            'montretrait' => 'montant du retrait',
            'libelle' => 'libellé',
            'description' => 'description',
            'etat' => 'état',
            'idcaisse' => 'caisse',
        ];
        $summaries = [];
        foreach (array_slice($changes, 0, 2, true) as $field => $values) {
            $fieldName = $fieldNames[strtolower((string) $field)] ?? str_replace('_', ' ', (string) $field);
            if (strtolower((string) $field) === 'montant') {
                $fieldName = match ($log->table_name) {
                    'caisses' => 'montant de caisse',
                    'depenses' => 'montant de dépense',
                    default => 'montant',
                };
            }
            $fieldDescription = in_array($fieldName, ['montant', 'libellé', 'description', 'état', 'caisse'], true)
                ? "{$fieldName} {$tableDescription}"
                : $fieldName;
            $value = is_array($values) ? ($values['new'] ?? null) : $values;
            if ($value === null || is_array($value) || is_object($value)) {
                $summaries[] = $fieldDescription;
                continue;
            }

            $displayValue = is_bool($value) ? ($value ? 'oui' : 'non') : (string) $value;
            $summaries[] = "{$fieldDescription} à {$displayValue}";
        }

        return "{$actionName} du " . implode(', ', $summaries) . $record;
    }
}
