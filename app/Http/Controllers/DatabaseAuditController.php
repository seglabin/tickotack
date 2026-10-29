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
            ->orderByDesc('logs.id')
            ->select([
                'logs.*',
                'users.name as user_name',
                'users.username as username',
            ])
            ->paginate(50)
            ->withQueryString();

        $users = DB::table('users')->orderBy('name')->get(['id', 'name', 'username']);
        $tables = DB::table('database_audit_logs')->distinct()->orderBy('table_name')->pluck('table_name');

        return view('back.historique-actions', compact('logs', 'users', 'tables'));
    }
}
