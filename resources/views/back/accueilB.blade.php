@extends('layoutback')

@section('content')
<style>
    .dashboard-team {
        --team-ink: #17233c;
        --team-muted: #7d899f;
        --team-line: #e9edf4;
        color: var(--team-ink);
        padding: 8px 0 38px;
    }
    .dashboard-team * { box-sizing: border-box; }
    .dashboard-team__hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 34px 38px;
        border: 1px solid #e8eef5;
        border-radius: 22px;
        background: linear-gradient(115deg, #f5f9ff 0%, #eef8f8 100%);
    }
    .dashboard-team__hero:after {
        content: "";
        position: absolute;
        width: 250px;
        height: 250px;
        right: 2%;
        top: -170px;
        border: 1px solid rgba(37, 116, 137, .12);
        border-radius: 50%;
        box-shadow: 0 0 0 32px rgba(37, 116, 137, .035), 0 0 0 66px rgba(37, 116, 137, .025);
        pointer-events: none;
    }
    .dashboard-team__hero-copy, .dashboard-team__role { position: relative; z-index: 1; }
    .dashboard-team__eyebrow {
        margin: 0 0 8px;
        color: #32828a;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .15em;
        text-transform: uppercase;
    }
    .dashboard-team__hero h1 { margin: 0; color: var(--team-ink); font-size: clamp(25px, 3vw, 35px); font-weight: 700; line-height: 1.2; }
    .dashboard-team__hero-copy > p:last-child { margin: 9px 0 0; color: #6d7c92; font-size: 14px; }
    .dashboard-team__role { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid rgba(44, 126, 138, .13); border-radius: 14px; background: rgba(255,255,255,.76); }
    .dashboard-team__role-icon { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 12px; color: #227d82; background: #e3f5f2; }
    .dashboard-team__role-label { display: block; margin-bottom: 3px; color: var(--team-muted); font-size: 10px; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; }
    .dashboard-team__role-name { display: block; color: var(--team-ink); font-size: 14px; font-weight: 700; }
    .dashboard-team__section-head { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin: 27px 0 15px; }
    .dashboard-team__section-head h2 { margin: 0; color: var(--team-ink); font-size: 19px; font-weight: 700; }
    .dashboard-team__section-head p { margin: 5px 0 0; color: var(--team-muted); font-size: 12px; }
    .dashboard-team__grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 15px; }
    .dashboard-team__card {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        min-height: 135px;
        padding: 20px;
        border: 1px solid var(--team-line);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(28, 48, 77, .035);
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .dashboard-team__card:hover { transform: translateY(-3px); border-color: #d2e5e8; box-shadow: 0 14px 28px rgba(28, 48, 77, .08); }
    .dashboard-team__card-icon { display: inline-flex; flex: 0 0 auto; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 13px; font-size: 16px; }
    .dashboard-team__card-icon--blue { color: #3476d2; background: #edf4ff; }
    .dashboard-team__card-icon--orange { color: #c38422; background: #fff6e7; }
    .dashboard-team__card-icon--purple { color: #7659c8; background: #f2efff; }
    .dashboard-team__card-icon--green { color: #34855c; background: #eaf7ee; }
    .dashboard-team__card-icon--teal { color: #16877f; background: #e9f8f5; }
    .dashboard-team__card-body { flex: 1; min-width: 0; }
    .dashboard-team__card h3 { margin: 1px 0 6px; color: var(--team-ink); font-size: 14px; font-weight: 700; }
    .dashboard-team__card p { margin: 0; color: var(--team-muted); font-size: 12px; line-height: 1.55; }
    .dashboard-team__card-arrow { margin-left: auto; padding-top: 4px; color: #9aa6b7; font-size: 12px; transition: transform .2s ease; }
    .dashboard-team__card:hover .dashboard-team__card-arrow { transform: translateX(3px); color: #287e89; }
    .dashboard-team__empty {
        padding: 30px;
        border: 1px dashed #d7e0ea;
        border-radius: 16px;
        color: var(--team-muted);
        background: #fbfcfe;
        text-align: center;
        font-size: 13px;
    }
    .dashboard-team__note { display: flex; align-items: center; gap: 11px; margin-top: 20px; padding: 14px 17px; border-radius: 12px; color: #5d7088; background: #f5f8fc; font-size: 12px; }
    .dashboard-team__note i { color: #4b87a2; }
    .dashboard-team__operations { margin-top: 27px; }
    .dashboard-team__operations-heading { margin-bottom: 14px; }
    .dashboard-team__operations-heading h2 { margin: 0; color: var(--team-ink); font-size: 19px; font-weight: 700; }
    .dashboard-team__operations-heading p { margin: 5px 0 0; color: var(--team-muted); font-size: 12px; }
    .dashboard-team__operation-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; }
    .dashboard-team__operation-panel { min-width: 0; overflow: hidden; border: 1px solid var(--team-line); border-radius: 16px; background: #fff; box-shadow: 0 8px 24px rgba(28, 48, 77, .035); }
    .dashboard-team__operation-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 20px; border-bottom: 1px solid var(--team-line); }
    .dashboard-team__operation-head h3 { margin: 0; color: var(--team-ink); font-size: 15px; font-weight: 700; }
    .dashboard-team__operation-head p { margin: 4px 0 0; color: var(--team-muted); font-size: 11px; }
    .dashboard-team__operation-link { color: #287e89; font-size: 11px; font-weight: 700; text-decoration: none; white-space: nowrap; }
    .dashboard-team__operation-table-wrap { overflow-x: auto; }
    .dashboard-team__operation-table { width: 100%; min-width: 450px; margin: 0; border-collapse: collapse; }
    .dashboard-team__operation-table th { padding: 11px 16px; color: #8a95a8; background: #fbfcfe; font-size: 10px; font-weight: 700; text-align: left; text-transform: uppercase; }
    .dashboard-team__operation-table td { padding: 13px 16px; border-top: 1px solid #f0f2f6; color: #46546b; font-size: 12px; }
    .dashboard-team__operation-ref { color: var(--team-ink); font-weight: 700; }
    .dashboard-team__operation-status { display: inline-block; padding: 4px 8px; border-radius: 20px; color: #547063; background: #edf7f1; font-size: 10px; font-weight: 600; }
    .dashboard-team__operation-status--pending { color: #9b6a1e; background: #fff5e4; }
    .dashboard-team__operation-empty { padding: 25px 18px; color: var(--team-muted); text-align: center; font-size: 12px; }
    @media (max-width: 991px) { .dashboard-team__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 575px) {
        .dashboard-team__hero { align-items: flex-start; flex-direction: column; padding: 26px 22px; }
        .dashboard-team__grid { grid-template-columns: 1fr; gap: 10px; }
        .dashboard-team__card { min-height: 110px; padding: 17px; }
        .dashboard-team__section-head { align-items: flex-start; flex-direction: column; }
        .dashboard-team__operation-grid { grid-template-columns: 1fr; gap: 10px; }
        .dashboard-team__operation-head { align-items: flex-start; }
    }
</style>

<section class="dashboard-team" aria-labelledby="dashboard-team-title">
    <div class="dashboard-team__hero">
        <div class="dashboard-team__hero-copy">
            <p class="dashboard-team__eyebrow">Espace de travail · {{ now()->format('d/m/Y') }}</p>
            <h1 id="dashboard-team-title">Bonjour, {{ Auth::user()->name }}</h1>
            <p>Retrouvez rapidement les outils nécessaires à votre activité.</p>
        </div>
        <div class="dashboard-team__role">
            <span class="dashboard-team__role-icon"><i class="fas fa-id-badge"></i></span>
            <span><span class="dashboard-team__role-label">Votre profil</span><span class="dashboard-team__role-name">{{ $roleLabel }}</span></span>
        </div>
    </div>

    <section class="dashboard-team__operations" aria-labelledby="dashboard-team-operations-title">
        <div class="dashboard-team__operations-heading">
            <h2 id="dashboard-team-operations-title">Activité de votre caisse</h2>
            <p>
                @if($userCashbox && $userCashbox->idadresse)
                    Expéditions et transferts à destination de {{ $userCashbox->adresse ?: 'l’adresse associée à votre caisse' }} ({{ $userCashbox->caisse }}).
                @else
                    Aucun point de caisse avec adresse de destination n’est associé à votre compte.
                @endif
            </p>
        </div>

        <div class="dashboard-team__operation-grid">
            <section class="dashboard-team__operation-panel" aria-labelledby="dashboard-team-expeditions-title">
                <div class="dashboard-team__operation-head">
                    <div>
                        <h3 id="dashboard-team-expeditions-title">Expéditions à recevoir</h3>
                        <p>Destinées à l’adresse de votre caisse.</p>
                    </div>
                    @if($userCashbox && $userCashbox->idadresse)
                        <a class="dashboard-team__operation-link" href="{{ url('expedition') }}">Tout voir <i class="fas fa-arrow-right"></i></a>
                    @endif
                </div>
                @if($recentExpeditions->isNotEmpty())
                    <div class="dashboard-team__operation-table-wrap">
                        <table class="dashboard-team__operation-table">
                            <thead><tr><th>Référence</th><th>Date</th><th>Expéditeur</th><th>État</th></tr></thead>
                            <tbody>
                                @foreach($recentExpeditions as $expedition)
                                    <tr>
                                        <td class="dashboard-team__operation-ref">{{ $expedition->code }}</td>
                                        <td>{{ \Carbon\Carbon::parse($expedition->datenreg)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $expedition->expediteur ?: '—' }}</td>
                                        <td><span class="dashboard-team__operation-status">{{ $expedition->etat ?: 'Enregistrée' }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="dashboard-team__operation-empty">
                        {{ $userCashbox && $userCashbox->idadresse ? 'Aucune expédition récente pour cette destination.' : 'Adresse de caisse non configurée.' }}
                    </div>
                @endif
            </section>

            <section class="dashboard-team__operation-panel" aria-labelledby="dashboard-team-transfers-title">
                <div class="dashboard-team__operation-head">
                    <div>
                        <h3 id="dashboard-team-transfers-title">Transferts à recevoir</h3>
                        <p>Destinés à l’adresse de votre caisse.</p>
                    </div>
                    @if($userCashbox && $userCashbox->idadresse)
                        <a class="dashboard-team__operation-link" href="{{ url('transfert') }}">Tout voir <i class="fas fa-arrow-right"></i></a>
                    @endif
                </div>
                @if($recentTransfers->isNotEmpty())
                    <div class="dashboard-team__operation-table-wrap">
                        <table class="dashboard-team__operation-table">
                            <thead><tr><th>Référence</th><th>Date</th><th>Expéditeur</th><th>Statut</th></tr></thead>
                            <tbody>
                                @foreach($recentTransfers as $transfer)
                                    <tr>
                                        <td class="dashboard-team__operation-ref">{{ $transfer->code }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transfer->datetransfert)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $transfer->expediteur ?: '—' }}</td>
                                        <td><span class="dashboard-team__operation-status {{ $transfer->datepayement ? '' : 'dashboard-team__operation-status--pending' }}">{{ $transfer->datepayement ? 'Payé' : 'À payer' }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="dashboard-team__operation-empty">
                        {{ $userCashbox && $userCashbox->idadresse ? 'Aucun transfert récent pour cette destination.' : 'Adresse de caisse non configurée.' }}
                    </div>
                @endif
            </section>
        </div>
    </section>

    <div class="dashboard-team__section-head">
        <div>
            <h2>Vos raccourcis</h2>
            <p>Accédez directement aux fonctionnalités autorisées pour votre profil.</p>
        </div>
    </div>

    @if(count($dashboardActions))
        <div class="dashboard-team__grid">
            @foreach($dashboardActions as $action)
                <a class="dashboard-team__card" href="{{ $action['url'] }}">
                    <span class="dashboard-team__card-icon dashboard-team__card-icon--{{ $action['color'] }}"><i class="fas {{ $action['icon'] }}"></i></span>
                    <span class="dashboard-team__card-body">
                        <h3>{{ $action['title'] }}</h3>
                        <p>{{ $action['description'] }}</p>
                    </span>
                    <i class="fas fa-arrow-right dashboard-team__card-arrow" aria-hidden="true"></i>
                </a>
            @endforeach
        </div>
    @else
        <div class="dashboard-team__empty">
            <i class="fas fa-lock"></i>
            <p class="mb-0 mt-2">Aucun raccourci n’est associé à votre profil. Contactez un administrateur pour demander un accès.</p>
        </div>
    @endif

    <div class="dashboard-team__note">
        <i class="fas fa-info-circle"></i>
        <span>Les raccourcis affichés correspondent aux droits d’accès de votre profil.</span>
    </div>
</section>
@endsection
