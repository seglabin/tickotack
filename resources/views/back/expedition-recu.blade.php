<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Reçu de retrait {{ $expedition->code }}</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        * { box-sizing: border-box; }
        body { color: #17202a; font-family: Arial, sans-serif; font-size: 9px; margin: 0; }
        .receipt-sheet { border-collapse: collapse; page-break-inside: avoid; table-layout: fixed; width: 100%; }
        .receipt { border: 1px solid #263746; height: 180mm; padding: 6mm; page-break-inside: avoid; position: relative; vertical-align: top; width: 50%; }
        .receipt:first-child { border-right: 1px dashed #7f8c8d; }
        .receipt:last-child { border-left: 0; }
        .header { border-bottom: 2px solid #263746; margin-bottom: 4mm; padding-bottom: 3mm; }
        .brand { font-size: 14px; font-weight: bold; }
        .copy { background: #eef5f7; border: 1px solid #8aa6b2; color: #263746; font-size: 8px; font-weight: bold; padding: 2mm; text-align: right; text-transform: uppercase; }
        .logo { max-height: 14mm; max-width: 55mm; }
        .code { font-size: 11px; font-weight: bold; text-align: center; }
        table { border-collapse: collapse; width: 100%; }
        .details { margin-top: 3mm; }
        .details td { border-bottom: 1px solid #d5dadd; padding: 2mm 1mm; vertical-align: top; width: 50%; }
        .receiver-card { display: block; margin-top: 1mm; max-height: 18mm; max-width: 38mm; }
        .label { color: #52616b; display: block; font-size: 7px; margin-bottom: 0.3mm; text-transform: uppercase; }
        .value { font-size: 9px; font-weight: bold; }
        .amount { background: #eef5f7; border: 1px solid #8aa6b2; font-size: 12px; margin-top: 3mm; padding: 3mm; text-align: center; }
        .signatures { bottom: 6mm; left: 6mm; page-break-inside: avoid; position: absolute; right: 6mm; }
        .signatures td { text-align: center; vertical-align: top; width: 50%; }
        .signature-space { height: 18mm; }
        .signature { border-top: 1px solid #263746; display: block; padding-top: 1mm; }
        .signature-name { font-weight: bold; margin-top: 2mm; }
        .notice { background: #eef5f7; margin: 3mm 0 0; padding: 2mm; text-align: center; }
    </style>
</head>
<body>
<table class="receipt-sheet">
    <tr>
@foreach (['Exemplaire client', 'Exemplaire caisse'] as $copie)
    <td class="receipt">
        <table class="header">
            <tr>
                <td><img class="logo" src="{{ public_path('assets/images/entete.png') }}" alt="Logo"></td>
                <td class="copy">{{ $copie }}<br>{{ date('d/m/Y', strtotime($retrait->datetat)) }}</td>
            </tr>
            <tr><td colspan="2" class="brand">REÇU DE RETRAIT DE COLIS</td></tr>
        </table>

        <div class="code">Colis N° {{ $expedition->code }}</div>
        <p style="margin: 2mm 0;">Je reconnais avoir reçu le colis décrit ci-dessous et confirme son retrait.</p>

        <table class="details">
            <tr>
                <td><span class="label">Date d'enregistrement</span><span class="value">{{ date('d/m/Y H:i', strtotime($expedition->datenreg)) }}</span></td>
                <td><span class="label">Date du retrait</span><span class="value">{{ date('d/m/Y', strtotime($retrait->datetat)) }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Expéditeur</span><span class="value">{{ $expedition->expediteur ?: '-' }}</span><br>{{ $expedition->telephone_expediteur ?: '' }}</td>
                <td>
                    <span class="label">Récepteur</span><span class="value">{{ $expedition->recepteur ?: '-' }}</span><br>{{ $expedition->telephone_recepteur ?: '' }}
                    @if($expedition->carte_recepteur && file_exists(public_path('storage/images/Acteurs/' . $expedition->carte_recepteur)))
                        <img class="receiver-card" src="{{ public_path('storage/images/Acteurs/' . $expedition->carte_recepteur) }}" alt="Carte du récepteur">
                    @endif
                </td>
            </tr>
            <tr>
                <td><span class="label">Départ</span><span class="value">{{ $expedition->villedepart ?: '-' }}</span></td>
                <td><span class="label">Destination</span><span class="value">{{ $expedition->villedestination ?: '-' }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Type de colis</span><span class="value">{{ $expedition->typecolis ?: '-' }}</span></td>
                <td><span class="label">Nombre de colis</span><span class="value">{{ $expedition->nbrcolis }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Poids</span><span class="value">{{ number_format((float) $expedition->poids, 2, ',', ' ') }} kg</span></td>
                <td><span class="label">Référence du retrait</span><span class="value">{{ $retrait->reference ?: '-' }}</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="label">Description / repère</span><span class="value">{{ $expedition->marque ?: '-' }}</span></td>
            </tr>
        </table>

        <div class="amount">
            Montant payé : {{ number_format((float) $expedition->montantpaye, 0, ',', ' ') }} CFA
        </div>
        <div class="notice">Reçu établi pour servir et valoir ce que de droit.</div>

        <table class="signatures">
            <tr>
                <td>
                    <div class="signature-space"></div>
                    <span class="signature">Signature du récepteur</span>
                    <div class="signature-name">{{ $expedition->recepteur ?: '-' }}</div>
                </td>
                <td>
                    <div class="signature-space"></div>
                    <span class="signature">Signature et cachet de l'agent</span>
                    <div class="signature-name">{{ $agent ?: '-' }}</div>
                </td>
            </tr>
        </table>
    </td>
@endforeach
    </tr>
</table>
</body>
</html>