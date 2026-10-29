@extends('layoutback', ['titre' => 'Historique des actions en base'])

@section('content')
<style>
  .audit-page { --audit-ink: #243746; --audit-muted: #748493; --audit-border: #e8edf2; }
  .audit-page .audit-heading { display: flex; align-items: center; gap: 14px; margin-bottom: 22px; }
  .audit-page .audit-heading-icon { display: grid; width: 48px; height: 48px; place-items: center; border-radius: 14px; color: #fff; background: linear-gradient(135deg, #286b83, #4a9b91); font-size: 19px; }
  .audit-page .audit-heading h3 { margin: 0 0 4px; color: var(--audit-ink); font-size: 21px; font-weight: 700; }
  .audit-page .audit-heading p { margin: 0; color: var(--audit-muted); font-size: 13px; }
  .audit-page .audit-card { overflow: hidden; border: 1px solid var(--audit-border); border-radius: 14px; background: #fff; box-shadow: 0 8px 26px rgba(34, 54, 72, .045); }
  .audit-page .audit-filter-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 22px; border-bottom: 1px solid var(--audit-border); }
  .audit-page .audit-filter-header h4 { margin: 0; color: var(--audit-ink); font-size: 15px; font-weight: 700; }
  .audit-page .audit-filter-body { padding: 20px 22px; }
  .audit-page .form-label { margin-bottom: 6px; color: #536574; font-size: 12px; font-weight: 600; }
  .audit-page .audit-filters { display: grid; grid-template-columns: minmax(210px, 1.5fr) repeat(4, minmax(135px, 1fr)) auto; align-items: end; gap: 14px; }
  .audit-page .audit-filter-field { min-width: 0; }
  .audit-page .audit-filter-field .form-control,
  .audit-page .audit-filter-field .form-select { width: 100%; min-width: 0; height: 42px; border: 1px solid #dfe6ec; border-radius: 8px; background-color: #fff; color: #344957; font-size: 13px; box-shadow: none; }
  .audit-page .audit-filter-field .form-control:focus,
  .audit-page .audit-filter-field .form-select:focus { border-color: #70a9ad; box-shadow: 0 0 0 3px rgba(70, 139, 143, .12); }
  .audit-page .audit-filter-actions { display: flex; gap: 8px; }
  .audit-page .audit-filter-actions .btn { display: inline-flex; height: 42px; align-items: center; justify-content: center; gap: 6px; border-radius: 8px; white-space: nowrap; }
  .audit-page .audit-stats { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin: 23px 0 12px; color: var(--audit-muted); font-size: 13px; }
  .audit-page .audit-stats strong { color: var(--audit-ink); }
  .audit-page .audit-table-wrap { overflow-x: auto; }
  .audit-page .audit-table { width: 100%; min-width: 1000px; margin: 0; border-collapse: separate; border-spacing: 0; table-layout: fixed; }
  .audit-page .audit-table th:nth-child(1) { width: 120px; }
  .audit-page .audit-table th:nth-child(2) { width: 190px; }
  .audit-page .audit-table th:nth-child(3) { width: 145px; }
  .audit-page .audit-table th:nth-child(4) { width: 165px; }
  .audit-page .audit-table th:nth-child(5) { width: 34%; }
  .audit-page .audit-table th:nth-child(6) { width: 230px; }
  .audit-page .audit-table thead th { padding: 13px 15px; border: 0; border-bottom: 1px solid var(--audit-border); background: #f6f8fa; color: #71808d; font-size: 10px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; white-space: nowrap; }
  .audit-page .audit-table tbody td { padding: 15px; border: 0; border-bottom: 1px solid #edf1f4; background: #fff; color: #40515e; font-size: 13px; vertical-align: top; }
  .audit-page .audit-table tbody tr:last-child td { border-bottom: 0; }
  .audit-page .audit-table tbody tr:hover td { background: #fbfcfd; }
  .audit-page .audit-table tbody tr { transition: background-color .15s ease; }
  .audit-page .audit-date { color: var(--audit-ink); font-weight: 600; white-space: nowrap; }
  .audit-page .audit-user { display: flex; align-items: center; gap: 9px; min-width: 150px; }
  .audit-page .audit-avatar { display: grid; width: 32px; height: 32px; flex: 0 0 32px; place-items: center; border-radius: 50%; background: #edf5f5; color: #337c78; font-size: 12px; font-weight: 700; }
  .audit-page .audit-user-name { color: var(--audit-ink); font-weight: 600; }
  .audit-page .audit-user-login { display: block; margin-top: 2px; color: var(--audit-muted); font-size: 11px; }
  .audit-page .audit-action { display: inline-flex; align-items: center; gap: 6px; padding: 5px 9px; border-radius: 20px; font-size: 10px; font-weight: 700; letter-spacing: .04em; }
  .audit-page .audit-action--insert { color: #19744d; background: #e9f7ef; }
  .audit-page .audit-action--update { color: #956319; background: #fff5df; }
  .audit-page .audit-action--delete { color: #a63c43; background: #fff0ef; }
  .audit-page .audit-action--replace { color: #5263a2; background: #eef0ff; }
  .audit-page .audit-table-name, .audit-page .audit-record-id, .audit-page .audit-ip { font-family: Consolas, monospace; font-size: 12px; }
  .audit-page .audit-table-name { color: #347879; }
  .audit-page .audit-changes { display: grid; gap: 6px; min-width: 0; }
  .audit-page .audit-change { display: grid; grid-template-columns: minmax(85px, .7fr) minmax(0, 1.3fr); gap: 8px; padding: 7px 9px; border: 1px solid #e8edf2; border-radius: 7px; background: #fafbfd; }
  .audit-page .audit-change-name { color: var(--audit-muted); font-size: 11px; overflow-wrap: anywhere; }
  .audit-page .audit-change-value { color: #40515e; font-family: Consolas, monospace; font-size: 11px; overflow-wrap: anywhere; }
  .audit-page .audit-more-changes { margin-top: 6px; color: #427c82; font-size: 11px; }
  .audit-page .audit-more-changes summary { width: fit-content; cursor: pointer; font-weight: 600; }
  .audit-page .audit-more-changes[open] summary { margin-bottom: 6px; }
  .audit-page .audit-route { display: block; overflow-wrap: anywhere; color: #40515e; }
  .audit-page .audit-empty { padding: 52px 20px !important; color: var(--audit-muted) !important; text-align: center; }
  .audit-page .audit-empty i { display: block; margin-bottom: 12px; color: #aab7c1; font-size: 30px; }
  .audit-page .audit-meta { display: block; margin-top: 4px; color: var(--audit-muted); font-size: 11px; }
  .audit-page .audit-meta summary { cursor: pointer; color: #427c82; }
  .audit-page .audit-pagination { display: flex; justify-content: flex-end; padding: 15px 20px; border-top: 1px solid var(--audit-border); }
  .audit-page .audit-pagination nav { margin: 0; }
  @media (max-width: 1199.98px) {
    .audit-page .audit-filters { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .audit-page .audit-filter-actions { grid-column: 1 / -1; }
  }
  @media (max-width: 767.98px) {
    .audit-page .audit-filter-header, .audit-page .audit-filter-body { padding: 15px; }
    .audit-page .audit-heading h3 { font-size: 18px; }
    .audit-page .audit-stats { align-items: flex-start; flex-direction: column; }
    .audit-page .audit-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .audit-page .audit-filter-field--user { grid-column: 1 / -1; }
    .audit-page .audit-filter-actions { flex-wrap: wrap; }
    .audit-page .audit-filter-actions .btn { flex: 1 1 140px; }
    .audit-page .audit-filter-header { align-items: flex-start; flex-direction: column; }
  }
  @media (max-width: 399.98px) { .audit-page .audit-filters { grid-template-columns: 1fr; } .audit-page .audit-filter-field--user, .audit-page .audit-filter-actions { grid-column: auto; } }
</style>

<div class="audit-page">
  <div class="audit-heading">
    <div class="audit-heading-icon"><i class="fa fa-history" aria-hidden="true"></i></div>
    <div>
      <h3>Journal des actions</h3>
      <p>Consultez les modifications effectuées dans la base de données.</p>
    </div>
  </div>

  <section class="audit-card" aria-labelledby="audit-filters-title">
    <div class="audit-filter-header">
      <h4 id="audit-filters-title"><i class="fa fa-sliders me-2" aria-hidden="true"></i>Filtrer l’historique</h4>
      <span class="text-muted small">Les valeurs sensibles sont masquées</span>
    </div>
    <div class="audit-filter-body">
      <div class="audit-filters">
        <div class="audit-filter-field audit-filter-field--user">
          <label for="user_id" class="form-label">Utilisateur</label>
          <select name="user_id" id="user_id" class="form-select">
            <option value="">Tous les utilisateurs</option>
            @foreach($users as $user)
            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }} ({{ $user->username }})</option>
            @endforeach
          </select>
        </div>
        <div class="audit-filter-field">
          <label for="table" class="form-label">Table concernée</label>
          <select name="table" id="table" class="form-select">
            <option value="">Toutes les tables</option>
            @foreach($tables as $table)
            <option value="{{ $table }}" @selected(request('table') === $table)>{{ $table }}</option>
            @endforeach
          </select>
        </div>
        <div class="audit-filter-field">
          <label for="action" class="form-label">Type d’action</label>
          <select name="action" id="action" class="form-select">
            <option value="">Toutes les actions</option>
            @foreach(['INSERT', 'UPDATE', 'DELETE', 'REPLACE'] as $action)
            <option value="{{ $action }}" @selected(request('action') === $action)>
              {{ ['INSERT' => 'Création', 'UPDATE' => 'Modification', 'DELETE' => 'Suppression', 'REPLACE' => 'Remplacement'][$action] }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="audit-filter-field">
          <label for="date_debut" class="form-label">Du</label>
          <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ request('date_debut') }}">
        </div>
        <div class="audit-filter-field">
          <label for="date_fin" class="form-label">Au</label>
          <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ request('date_fin') }}">
        </div>
        <div class="audit-filter-actions">
          <button type="button" class="btn btn-primary" onclick="appliquerFiltresHistorique()"><i class="fa fa-search" aria-hidden="true"></i>Appliquer</button>
          <a href="{{ route('admin.historique-actions') }}" class="btn btn-outline-secondary"><i class="fa fa-undo me-1" aria-hidden="true"></i>Réinitialiser</a>
        </div>
      </div>
    </div>
  </section>

  <div class="audit-stats">
    <div><strong>{{ number_format($logs->total(), 0, ',', ' ') }}</strong> action(s) trouvée(s)</div>
    <div>Affichage de <strong>{{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }}</strong> sur <strong>{{ number_format($logs->total(), 0, ',', ' ') }}</strong></div>
  </div>

  <section class="audit-card" aria-label="Actions enregistrées">
    <div class="audit-table-wrap">
      <table class="audit-table">
        <thead>
          <tr>
            <th>Date et heure</th>
            <th>Utilisateur</th>
            <th>Action</th>
            <th>Table / enregistrement</th>
            <!-- <th>Changements</th> -->
            <th>Origine</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
          @php
            $nomUtilisateur = $log->user_name ?? $log->username ?? 'Utilisateur supprimé';
            $changements = $log->changes ? json_decode($log->changes, true) : [];
            $libellesActions = ['INSERT' => 'Création', 'UPDATE' => 'Modification', 'DELETE' => 'Suppression', 'REPLACE' => 'Remplacement'];
          @endphp
          <tr>
            <td class="audit-date">{{ \Illuminate\Support\Carbon::parse($log->created_at)->format('d/m/Y') }}<span class="audit-meta">{{ \Illuminate\Support\Carbon::parse($log->created_at)->format('H:i:s') }}</span></td>
            <td>
              <div class="audit-user">
                <span class="audit-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($nomUtilisateur, 0, 1)) }}</span>
                <span><span class="audit-user-name">{{ $nomUtilisateur }}</span><span class="audit-user-login">{{ $log->username ?? 'Compte supprimé' }}</span></span>
              </div>
            </td>
            <td>
              @php($classeAction = strtolower($log->action))
              <span class="audit-action audit-action--{{ $classeAction }}">
                <i class="fa {{ ['INSERT' => 'fa-plus', 'UPDATE' => 'fa-pencil', 'DELETE' => 'fa-trash', 'REPLACE' => 'fa-refresh'][$log->action] ?? 'fa-database' }}" aria-hidden="true"></i>
                {{ $libellesActions[$log->action] ?? $log->action }}
              </span>
            </td>
            <td><span class="audit-table-name">{{ $log->table_name }}</span><span class="audit-meta">ID : <span class="audit-record-id">{{ $log->record_id ?? '—' }}</span></span></td>
            <!-- <td>
              @if(is_array($changements) && count($changements))
              <div class="audit-changes">
                @foreach(array_slice($changements, 0, 3, true) as $champ => $valeurs)
                <span class="audit-change">
                  <span class="audit-change-name">{{ $champ }}</span>
                  <span class="audit-change-value">{{ is_array($valeurs) ? ($valeurs['new'] ?? json_encode($valeurs, JSON_UNESCAPED_UNICODE)) : $valeurs }}</span>
                </span>
                @endforeach
              </div>
              @if(count($changements) > 3)
              <details class="audit-more-changes">
                <summary>Voir {{ count($changements) - 3 }} autre(s) champ(s)</summary>
                <div class="audit-changes">
                  @foreach(array_slice($changements, 3, null, true) as $champ => $valeurs)
                  <span class="audit-change">
                    <span class="audit-change-name">{{ $champ }}</span>
                    <span class="audit-change-value">{{ is_array($valeurs) ? ($valeurs['new'] ?? json_encode($valeurs, JSON_UNESCAPED_UNICODE)) : $valeurs }}</span>
                  </span>
                  @endforeach
                </div>
              </details>
              @endif
              @else
              <span class="text-muted">Détails non disponibles</span>
              @endif
            </td> -->
            <td>
              <span class="audit-route">{{ $log->route ?? 'Action système' }}</span>
              <span class="audit-meta audit-ip">{{ $log->ip_address ?? 'IP inconnue' }}</span>
              @if($log->user_agent)
              <details class="audit-meta">
                <summary>Appareil / navigateur</summary>
                <span>{{ $log->user_agent }}</span>
              </details>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="audit-empty">
              <i class="fa fa-search" aria-hidden="true"></i>
              <strong>Aucune action trouvée</strong>
              <div class="mt-1">Essayez de modifier les filtres ou réinitialisez la recherche.</div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($logs->hasPages())
    <div class="audit-pagination">{{ $logs->links() }}</div>
    @endif
  </section>
</div>
<script>
  function appliquerFiltresHistorique() {
    const url = new URL(window.location.href);
    ['user_id', 'table', 'action', 'date_debut', 'date_fin'].forEach(function (name) {
      const valeur = document.getElementById(name).value;
      if (valeur) {
        url.searchParams.set(name, valeur);
      } else {
        url.searchParams.delete(name);
      }
    });
    url.searchParams.delete('page');
    window.location.assign(url.toString());
  }
</script>
@endsection
