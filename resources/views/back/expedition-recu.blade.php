<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Reçu de retrait {{ $expedition->code }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 6mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #17202a;
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
        }

        .receipt-sheet {
            border-collapse: separate;
            border-spacing: 5mm 0;
            page-break-inside: avoid;
            table-layout: fixed;
            width: 100%;
        }

        .receipt {
            border: 1px solid #263746;
            height: 180mm;
            padding: 4mm;
            page-break-inside: avoid;
            vertical-align: top;
        }

        .receipt-header,
        .title-row,
        .details,
        .receipt-signatures {
            border-collapse: collapse;
            width: 100%;
        }

        .receipt-header {
            border-bottom: 2px solid #263746;
            margin-bottom: 3mm;
        }

        .receipt-header td {
            padding: 0 0 3mm;
            vertical-align: top;
        }

        .logo {
            max-height: 15mm;
            width: 80mm;
        }

        .title-row {
            border-bottom: 1px solid #d5dadd;
        }

        .title-row td {
            padding: 0 0 2mm;
            vertical-align: top;
        }

        .title-row td+td {
            width: 32%;
        }

        .brand {
            font-size: 14px;
            font-weight: bold;
        }

        .location {
            color: #52616b;
            text-align: right;
        }

        .copy {
            background: #eef5f7;
            border: 1px solid #8aa6b2;
            color: #263746;
            font-size: 9px;
            font-weight: bold;
            margin-top: 2mm;
            padding: 2mm;
            text-align: center;
            text-transform: uppercase;
        }

        .code {
            color: #52616b;
            font-size: 9px;
            margin-top: 2mm;
        }

        .statement {
            line-height: 1.5;
            margin: 4mm 0;
        }

        .details {
            border: 1px solid #d5dadd;
            margin: 3mm 0;
        }

        .details td {
            border: 1px solid #d5dadd;
            padding: 2mm;
            vertical-align: top;
            width: 50%;
        }

        .label {
            color: #52616b;
            display: block;
            font-size: 8px;
            margin-bottom: 1mm;
            text-transform: uppercase;
        }

        .value {
            font-size: 10px;
            font-weight: bold;
        }

        .receiver-card {
            display: block;
            margin: auto;
            height: 22mm;
            width: 28mm;
            /* max-height: 22mm;
            max-width: 38mm; */
        }

        .amount {
            background: #eef5f7;
            border: 1px solid #8aa6b2;
            font-size: 13px;
            font-weight: bold;
            margin: 2mm 0;
            padding: 2mm;
            text-align: center;
        }

        .acknowledgment {
            line-height: 1;
            margin-top: 2mm;
            text-align: center;
        }

        .receipt-signatures {
            margin-top: 2mm;
        }

        .receipt-signatures td {
            text-align: center;
            vertical-align: top;
            width: 50%;
        }

        .signature-space {
            height: 1Omm;
        }

        .signature-label {
            border-top: 1px solid #263746;
            display: block;
            padding-top: 2mm;
        }

        .signer {
            font-weight: bold;
            margin-top: 6mm;
        }
    </style>
</head>
@php
$logoPath = public_path('assets/images/entete.png');
$logoSource = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
$photoPath = $expedition->carte_recepteur
    ? public_path('storage/images/Acteurs/' . $expedition->carte_recepteur)
    : null;
$photoInfo = $photoPath && is_file($photoPath) ? getimagesize($photoPath) : false;
$photoSource = $photoInfo
    ? 'data:' . $photoInfo['mime'] . ';base64,' . base64_encode(file_get_contents($photoPath))
    : null;
@endphp
<body>
    <table class="receipt-sheet">
        <tr>
            @foreach (['Exemplaire client', 'Exemplaire caisse'] as $copie)
            <td class="receipt">
                <table class="receipt-header">
                    <tr>
                        <td><img class="logo" src="{{ $logoSource }}" alt="Global Vision"></td>
                    </tr>
                </table>

                <table class="title-row">
                    <tr>
                        <td>
                            <div class="brand">REÇU DE RETRAIT DE COLIS</div>
                            <div class="code">Référence : {{ $expedition->code ?: '-' }}</div>
                        </td>
                        <td>
                            <div class="location">
                                {{ $expedition->villedestination ?: '-' }} le {{ date('d/m/Y H:i', strtotime($retrait->datetat)) }}
                            </div>
                            <div class="copy">{{ $copie }}</div>
                        </td>
                    </tr>
                </table>

                <div class="statement">
                    Je reconnais avoir reçu le colis décrit ci-dessous et confirme son retrait.
                </div>

                <table class="details">
                    <tr>
                        <td>
                            <span class="label">Date d'enregistrement</span>
                            <span class="value">{{ date('d/m/Y H:i', strtotime($expedition->datenreg)) }}</span>
                        </td>
                        <td>
                            <span class="label">Date du retrait</span>
                            <span class="value">{{ date('d/m/Y', strtotime($retrait->datetat)) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="label">Expéditeur</span>
                            <span class="value">{{ $expedition->expediteur ?: '-' }}</span><br>
                            {{ $expedition->telephone_expediteur ?: '' }}
                        </td>
                        <td >
                            <div >
                                <table>
                                    <tr>
                                        
                                        <td>
                                            <span class="label">Récepteur</span>
                                            <span class="value">{{ $expedition->recepteur ?: '-' }}</span><br>
                                            {{ $expedition->telephone_recepteur ?: '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td >
                                            @if($photoSource)
                                            <img class="receiver-card" src="{{ $photoSource }}" alt="Carte du récepteur">
                                            @endif
                                        </td>
                                    </tr>
                                    
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="label">Départ</span>
                            <span class="value">{{ $expedition->villedepart ?: '-' }}</span>
                        </td>
                        <td>
                            <span class="label">Destination</span>
                            <span class="value">{{ $expedition->villedestination ?: '-' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="label">Type de colis</span>
                            <span class="value">{{ $expedition->typecolis ?: '-' }}</span>
                        </td>
                        <td>
                            <span class="label">Nombre de colis</span>
                            <span class="value">{{ $expedition->nbrcolis }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="label">Poids</span>
                            <span class="value">{{ number_format((float) $expedition->poids, 2, ',', ' ') }} kg</span>
                        </td>
                        <td>
                            <span class="label">Référence du retrait</span>
                            <span class="value">{{ $retrait->reference ?: '-' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="label">Description / repère</span>
                            <span class="value">{{ $expedition->marque ?: '-' }}</span>
                        </td>
                    </tr>
                </table>

                <div class="amount">
                    Montant payé : {{ number_format((float) $expedition->montantpaye, 0, ',', ' ') }} CFA
                </div>
                <div class="acknowledgment">Reçu établi pour servir et valoir ce que de droit.</div>

                <table class="receipt-signatures">
                    <tr>
                        <td>
                            <!-- <div class="signature-space"></div> -->
                            <span class="signature-label">Signature du récepteur</span>
                            <div class="signer">{{ $expedition->recepteur ?: '-' }}</div>

                        </td>
                        <td>
                            <div class="signature-space"></div>
                            <span class="signature-label">Signature et cachet de l'agent</span>
                            <div class="signer">{{ $agent ?: '-' }}</div>
                        </td>
                    </tr>
                </table>
            </td>
            @endforeach
        </tr>
    </table>
    @if($autoPrint ?? false)
    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
    @endif
</body>

</html>