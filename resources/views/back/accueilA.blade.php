@extends('layoutback')

@section('content')
<style>
    .dashboard-admin {
        --dash-ink: #17233c;
        --dash-muted: #7d899f;
        --dash-line: #e9edf4;
        color: var(--dash-ink);
        padding: 8px 0 38px;
    }
    .dashboard-admin * { box-sizing: border-box; }
    .dashboard-admin__hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 34px 38px;
        border-radius: 22px;
        color: #fff;
        background: linear-gradient(115deg, #142746 0%, #1c4772 62%, #287e89 100%);
        box-shadow: 0 18px 38px rgba(24, 59, 94, .16);
    }
    .dashboard-admin__hero:after {
        content: "";
        position: absolute;
        width: 290px;
        height: 290px;
        right: 8%;
        top: -190px;
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 50%;
        box-shadow: 0 0 0 34px rgba(255,255,255,.035), 0 0 0 70px rgba(255,255,255,.025);
        pointer-events: none;
    }
    .dashboard-admin__hero-copy, .dashboard-admin__hero-actions { position: relative; z-index: 1; }
    .dashboard-admin__eyebrow {
        margin: 0 0 8px;
        color: #a9d4dc;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
    }
    .dashboard-admin__hero h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(25px, 3vw, 35px);
        font-weight: 700;
        line-height: 1.2;
    }
    .dashboard-admin__hero p:last-child { margin: 9px 0 0; color: rgba(255,255,255,.76); font-size: 14px; }
    .dashboard-admin__hero-actions { display: flex; flex-wrap: wrap; gap: 10px; }
    .dashboard-admin__button {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 11px 15px;
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 11px;
        color: #fff;
        background: rgba(255,255,255,.11);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: background .2s ease, transform .2s ease;
    }
    .dashboard-admin__button:hover { color: #fff; background: rgba(255,255,255,.2); transform: translateY(-1px); }
    .dashboard-admin__stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin: 23px 0;
    }
    .dashboard-admin__stat {
        display: flex;
        align-items: center;
        gap: 15px;
        min-height: 112px;
        padding: 20px;
        border: 1px solid var(--dash-line);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(28, 48, 77, .035);
    }
    .dashboard-admin__stat-icon, .dashboard-admin__table-icon {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border-radius: 13px;
        font-size: 17px;
    }
    .dashboard-admin__stat-icon--blue { color: #3476d2; background: #edf4ff; }
    .dashboard-admin__stat-icon--teal { color: #16877f; background: #e9f8f5; }
    .dashboard-admin__stat-icon--violet { color: #7659c8; background: #f2efff; }
    .dashboard-admin__stat-icon--amber { color: #c38422; background: #fff6e7; }
    .dashboard-admin__stat-label { margin: 0 0 4px; color: var(--dash-muted); font-size: 12px; font-weight: 600; }
    .dashboard-admin__stat-value { margin: 0; color: var(--dash-ink); font-size: 25px; font-weight: 700; line-height: 1.2; }
    .dashboard-admin__panel {
        overflow: hidden;
        border: 1px solid var(--dash-line);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(28, 48, 77, .035);
    }
    .dashboard-admin__panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 23px;
        border-bottom: 1px solid var(--dash-line);
    }
    .dashboard-admin__panel-head h2 { margin: 0; color: var(--dash-ink); font-size: 17px; font-weight: 700; }
    .dashboard-admin__panel-head p { margin: 4px 0 0; color: var(--dash-muted); font-size: 12px; }
    .dashboard-admin__link { color: #287e89; font-size: 12px; font-weight: 700; text-decoration: none; white-space: nowrap; }
    .dashboard-admin__table-wrap { overflow-x: auto; }
    .dashboard-admin__table { width: 100%; min-width: 540px; margin: 0; border-collapse: collapse; }
    .dashboard-admin__table th {
        padding: 12px 22px;
        color: #8a95a8;
        background: #fbfcfe;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .08em;
        text-align: left;
        text-transform: uppercase;
    }
    .dashboard-admin__table td { padding: 14px 22px; border-top: 1px solid #f0f2f6; color: #46546b; font-size: 13px; }
    .dashboard-admin__table-code { color: #1d2c46; font-weight: 700; }
    .dashboard-admin__table-icon { width: 34px; height: 34px; margin-right: 9px; color: #388c87; background: #eaf7f5; font-size: 13px; vertical-align: middle; }
    .dashboard-admin__badge { display: inline-block; padding: 5px 9px; border-radius: 20px; color: #547063; background: #edf7f1; font-size: 11px; font-weight: 600; }
    .dashboard-admin__badge--pending { color: #9b6a1e; background: #fff5e4; }
    .dashboard-admin__empty { padding: 34px 20px; color: var(--dash-muted); text-align: center; font-size: 13px; }
    .dashboard-admin__chart { padding: 8px 23px 22px; }
    .dashboard-admin__chart-legend { display: flex; flex-wrap: wrap; gap: 18px; margin: 6px 0 20px; color: var(--dash-muted); font-size: 11px; }
    .dashboard-admin__legend-item { display: inline-flex; align-items: center; gap: 7px; }
    .dashboard-admin__legend-dot { width: 9px; height: 9px; border-radius: 50%; }
    .dashboard-admin__legend-dot--expedition { background: #2563eb; }
    .dashboard-admin__legend-dot--transfer { background: #c47a55; }
    .dashboard-admin__chart-note { margin-left: auto; color: #98a2b2; }
    .dashboard-admin__cashbox { padding: 16px 0; border-top: 1px solid #f0f2f6; }
    .dashboard-admin__cashbox:first-of-type { border-top: 0; }
    .dashboard-admin__cashbox-title { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 11px; color: #263650; font-size: 13px; font-weight: 700; }
    .dashboard-admin__cashbox-currency { color: #8490a3; font-size: 10px; font-weight: 600; }
    .dashboard-admin__bar-row { display: grid; grid-template-columns: 130px minmax(60px, 1fr) 145px; align-items: center; gap: 12px; margin-top: 8px; }
    .dashboard-admin__bar-label { color: #738097; font-size: 11px; }
    .dashboard-admin__bar-track { overflow: hidden; height: 8px; border-radius: 8px; background: #f0f3f7; }
    .dashboard-admin__bar { height: 100%; min-width: 0; border-radius: inherit; transition: width .3s ease; }
    .dashboard-admin__bar--expedition { background: linear-gradient(90deg, #60a5fa, #2563eb); }
    .dashboard-admin__bar--transfer { background: linear-gradient(90deg, #e8b99b, #c47a55); }
    .dashboard-admin__bar-value { color: #45546b; font-size: 11px; font-weight: 700; text-align: right; white-space: nowrap; }
    .dashboard-admin__activity-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-top: 18px; }
    .dashboard-admin__activity-panel { min-width: 0; }
    .dashboard-admin__table--compact { min-width: 470px; }
    .dashboard-admin__table--compact th, .dashboard-admin__table--compact td { padding-right: 16px; padding-left: 16px; }
    .dashboard-admin__advances { margin-top: 18px; }
    @media (max-width: 991px) {
        .dashboard-admin__stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .dashboard-admin__activity-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 575px) {
        .dashboard-admin__hero { align-items: flex-start; flex-direction: column; padding: 26px 22px; }
        .dashboard-admin__stats { gap: 10px; }
        .dashboard-admin__stat { align-items: flex-start; flex-direction: column; gap: 12px; min-height: 132px; padding: 15px; }
        .dashboard-admin__stat-value { font-size: 22px; }
        .dashboard-admin__panel-head { align-items: flex-start; }
        .dashboard-admin__chart { padding-right: 16px; padding-left: 16px; }
        .dashboard-admin__bar-row { grid-template-columns: 94px minmax(35px, 1fr) 104px; gap: 7px; }
        .dashboard-admin__bar-label, .dashboard-admin__bar-value { font-size: 10px; }
        .dashboard-admin__chart-note { width: 100%; margin-left: 0; }
    }
</style>

<section class="dashboard-admin" aria-labelledby="dashboard-admin-title">
    <div class="dashboard-admin__hero">
        <div class="dashboard-admin__hero-copy">
            <p class="dashboard-admin__eyebrow">Espace d’administration · {{ now()->format('d/m/Y') }}</p>
            <h1 id="dashboard-admin-title">Bonjour, {{ Auth::user()->name }}</h1>
            <p>La vue d’ensemble de votre activité, réunie au même endroit.</p>
        </div>
        <div class="dashboard-admin__hero-actions">
            <a class="dashboard-admin__button" href="{{ url('expedition') }}"><i class="fas fa-box"></i> Expéditions</a>
            <a class="dashboard-admin__button" href="{{ route('utilisateur') }}"><i class="fas fa-user-cog"></i> Utilisateurs</a>
        </div>
    </div>

    <div class="dashboard-admin__stats" aria-label="Indicateurs du jour">
        <article class="dashboard-admin__stat">
            <span class="dashboard-admin__stat-icon dashboard-admin__stat-icon--blue"><i class="fas fa-users"></i></span>
            <div><p class="dashboard-admin__stat-label">Utilisateurs actifs</p><p class="dashboard-admin__stat-value">{{ number_format($dashboardStats['users'], 0, ',', ' ') }}</p></div>
        </article>
        <article class="dashboard-admin__stat">
            <span class="dashboard-admin__stat-icon dashboard-admin__stat-icon--teal"><i class="fas fa-box"></i></span>
            <div><p class="dashboard-admin__stat-label">Expéditions aujourd’hui</p><p class="dashboard-admin__stat-value">{{ number_format($dashboardStats['expeditions'], 0, ',', ' ') }}</p></div>
        </article>
        <article class="dashboard-admin__stat">
            <span class="dashboard-admin__stat-icon dashboard-admin__stat-icon--violet"><i class="fas fa-exchange-alt"></i></span>
            <div><p class="dashboard-admin__stat-label">Transferts aujourd’hui</p><p class="dashboard-admin__stat-value">{{ number_format($dashboardStats['transfers'], 0, ',', ' ') }}</p></div>
        </article>
        <article class="dashboard-admin__stat">
            <span class="dashboard-admin__stat-icon dashboard-admin__stat-icon--amber"><i class="fas fa-cash-register"></i></span>
            <div><p class="dashboard-admin__stat-label">Caisses configurées</p><p class="dashboard-admin__stat-value">{{ number_format($dashboardStats['cashboxes'], 0, ',', ' ') }}</p></div>
        </article>
        <article class="dashboard-admin__stat">
            <span class="dashboard-admin__stat-icon dashboard-admin__stat-icon--amber"><i class="fas fa-hourglass-half"></i></span>
            <div><p class="dashboard-admin__stat-label">Colis avec solde à payer</p><p class="dashboard-admin__stat-value">{{ number_format($dashboardStats['pendingShipments'], 0, ',', ' ') }}</p></div>
        </article>
        <article class="dashboard-admin__stat">
            <span class="dashboard-admin__stat-icon dashboard-admin__stat-icon--violet"><i class="fas fa-hand-holding-usd"></i></span>
            <div><p class="dashboard-admin__stat-label">Transferts à payer</p><p class="dashboard-admin__stat-value">{{ number_format($dashboardStats['pendingTransfers'], 0, ',', ' ') }}</p></div>
        </article>
    </div>

    <section class="dashboard-admin__panel" aria-labelledby="dashboard-payment-title">
        <div class="dashboard-admin__panel-head">
            <div>
                <h2 id="dashboard-payment-title">Paiements par caisse</h2>
                <p>Comparaison des montants encaissés pour les expéditions et les transferts.</p>
            </div>
            <a class="dashboard-admin__link" href="{{ url('caisse') }}">Gérer les caisses <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="dashboard-admin__chart">
            <div class="dashboard-admin__chart-legend">
                <span class="dashboard-admin__legend-item"><span class="dashboard-admin__legend-dot dashboard-admin__legend-dot--expedition"></span>Expéditions payées</span>
                <span class="dashboard-admin__legend-item"><span class="dashboard-admin__legend-dot dashboard-admin__legend-dot--transfer"></span>Transferts payés</span>
                <span class="dashboard-admin__chart-note">Échelle relative à chaque caisse · devise indiquée par caisse</span>
            </div>
            @forelse($cashboxPaymentComparison as $cashbox)
                @php
                    $maxPaid = max((float) $cashbox->expeditions_payees, (float) $cashbox->transferts_payes);
                    $expeditionWidth = $maxPaid > 0 ? ((float) $cashbox->expeditions_payees / $maxPaid) * 100 : 0;
                    $transferWidth = $maxPaid > 0 ? ((float) $cashbox->transferts_payes / $maxPaid) * 100 : 0;
                @endphp
                <div class="dashboard-admin__cashbox">
                    <div class="dashboard-admin__cashbox-title">
                        <span>{{ $cashbox->libelle }}</span>
                        <span class="dashboard-admin__cashbox-currency">{{ $cashbox->devise }}</span>
                    </div>
                    <div class="dashboard-admin__bar-row">
                        <span class="dashboard-admin__bar-label">Expéditions</span>
                        <div class="dashboard-admin__bar-track" role="img" aria-label="Montant des expéditions payées">
                            <div class="dashboard-admin__bar dashboard-admin__bar--expedition" style="width: {{ $expeditionWidth }}%"></div>
                        </div>
                        <span class="dashboard-admin__bar-value">{{ number_format((float) $cashbox->expeditions_payees, 2, ',', ' ') }} {{ $cashbox->devise }}</span>
                    </div>
                    <div class="dashboard-admin__bar-row">
                        <span class="dashboard-admin__bar-label">Transferts</span>
                        <div class="dashboard-admin__bar-track" role="img" aria-label="Montant des transferts payés">
                            <div class="dashboard-admin__bar dashboard-admin__bar--transfer" style="width: {{ $transferWidth }}%"></div>
                        </div>
                        <span class="dashboard-admin__bar-value">{{ number_format((float) $cashbox->transferts_payes, 2, ',', ' ') }} {{ $cashbox->devise }}</span>
                    </div>
                </div>
            @empty
                <div class="dashboard-admin__empty">Aucune caisse n’est configurée pour le moment.</div>
            @endforelse
        </div>
    </section>

    <section class="dashboard-admin__panel dashboard-admin__advances" aria-labelledby="dashboard-advances-title">
        <div class="dashboard-admin__panel-head">
            <div>
                <h2 id="dashboard-advances-title">Approvisionnements de caisse</h2>
                <p>Les cinq derniers approvisionnements, visibles uniquement dans l’espace administrateur.</p>
            </div>
            <a class="dashboard-admin__link" href="{{ url('approcaisse') }}">Tout voir <i class="fas fa-arrow-right"></i></a>
        </div>
        @if($recentCashAdvances->isNotEmpty())
            <div class="dashboard-admin__table-wrap">
                <table class="dashboard-admin__table">
                    <thead>
                        <tr><th>Date</th><th>Caisse</th><th>Source</th><th>Observation</th><th>Montant</th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentCashAdvances as $advance)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($advance->dateappro)->format('d/m/Y H:i') }}</td>
                                <td><span class="dashboard-admin__table-code">{{ $advance->caisse ?: '—' }}</span></td>
                                <td>{{ $advance->source ?: '—' }}</td>
                                <td>{{ $advance->observation ?: '—' }}</td>
                                <td>{{ number_format((float) $advance->montant, 2, ',', ' ') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="dashboard-admin__empty">Aucun approvisionnement de caisse n’a encore été enregistré.</div>
        @endif
    </section>

    <div class="dashboard-admin__activity-grid">
        <section class="dashboard-admin__panel dashboard-admin__activity-panel" aria-labelledby="dashboard-activity-title">
            <div class="dashboard-admin__panel-head">
                <div>
                    <h2 id="dashboard-activity-title">Expéditions récentes</h2>
                    <p>Les cinq dernières expéditions enregistrées.</p>
                </div>
                <a class="dashboard-admin__link" href="{{ url('expedition') }}">Tout voir <i class="fas fa-arrow-right"></i></a>
            </div>
            @if($recentExpeditions->isNotEmpty())
                <div class="dashboard-admin__table-wrap">
                    <table class="dashboard-admin__table dashboard-admin__table--compact">
                        <thead>
                            <tr><th>Référence</th><th>Date</th><th>Destination</th><th>État</th></tr>
                        </thead>
                        <tbody>
                            @foreach($recentExpeditions as $expedition)
                                <tr>
                                    <td><span class="dashboard-admin__table-icon"><i class="fas fa-box"></i></span><span class="dashboard-admin__table-code">{{ $expedition->code }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($expedition->datenreg)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $expedition->destination ?: '—' }}</td>
                                    <td><span class="dashboard-admin__badge">{{ $expedition->etat ?: 'Enregistrée' }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="dashboard-admin__empty">Aucune expédition n’a encore été enregistrée.</div>
            @endif
        </section>

        <section class="dashboard-admin__panel dashboard-admin__activity-panel" aria-labelledby="dashboard-transfers-title">
            <div class="dashboard-admin__panel-head">
                <div>
                    <h2 id="dashboard-transfers-title">Transferts récents</h2>
                    <p>Derniers transferts et montant réglé.</p>
                </div>
                <a class="dashboard-admin__link" href="{{ url('transfert') }}">Tout voir <i class="fas fa-arrow-right"></i></a>
            </div>
            @if($recentTransfers->isNotEmpty())
                <div class="dashboard-admin__table-wrap">
                    <table class="dashboard-admin__table dashboard-admin__table--compact">
                        <thead>
                            <tr><th>Référence</th><th>Date</th><th>Destinataire</th><th>Payé</th><th>Statut</th></tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransfers as $transfer)
                                <tr>
                                    <td><span class="dashboard-admin__table-icon"><i class="fas fa-exchange-alt"></i></span><span class="dashboard-admin__table-code">{{ $transfer->code }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($transfer->datetransfert)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $transfer->recepteur ?: '—' }}<br><small>{{ $transfer->destination ?: 'Destination inconnue' }}</small></td>
                                    <td>{{ number_format((float) $transfer->montpaye, 2, ',', ' ') }}</td>
                                    <td>
                                        <span class="dashboard-admin__badge {{ $transfer->datepayement ? '' : 'dashboard-admin__badge--pending' }}">
                                            {{ $transfer->datepayement ? 'Payé' : 'À payer' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="dashboard-admin__empty">Aucun transfert n’a encore été enregistré.</div>
            @endif
        </section>
    </div>
</section>
@endsection
