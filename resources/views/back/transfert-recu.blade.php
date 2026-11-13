<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Reçu {{ $transfert->code }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin:8mm 6mm 8mm 6mm;
            /* margin: 8mm; */
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
            border-spacing: 5mm 0;/* Espacement entre les cadres  des exemples */
            page-break-inside: avoid;
            table-layout: fixed;
            width: 100%;
        }

        .receipt {
            border: 1px solid #263746;
            height: 180mm;
            padding: 6mm;
            page-break-inside: avoid;
            position: relative;
            vertical-align: top;
        }

        .receipt-header,
        .receipt-signatures {
            border-collapse: collapse;
            width: 100%;
        }

        .receipt-header {
            border-bottom: 2px solid #263746;
            margin-bottom: 4mm;
        }

        .receipt-header td {
            padding: 0 0 3mm;
            vertical-align: top;
        }

        .logo {
            max-height: 15mm;
            width: 70mm;
        }

        /* .logo { max-height: 16mm; max-width: 62mm; } */
        .location {
            color: #52616b;
            text-align: right;
        }

        .title-row {
            border-bottom: 1px solid #d5dadd;
            /* padding-bottom: 3mm; */
            width: 100%;
        }

        .brand {
            font-size: 14px;
            font-weight: bold;
        }

        .copy {
            background: #eef5f7;
            border: 1px solid #8aa6b2;
            color: #263746;
            font-size: 9px;
            font-weight: bold;
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
            line-height: 1.6;
            margin: 1mm 0;
        }

        .value {
            font-size: 11px;
            font-weight: bold;
        }

        .amounts {
            background: #eef5f7;
            border: 1px solid #8aa6b2;
            border-collapse: collapse;
            margin: 3mm 0;
            width: 100%;
        }

        .amounts td {
            padding: 3mm;
            vertical-align: top;
            width: 50%;
        }

        .amounts td+td {
            border-left: 1px solid #8aa6b2;
        }

        .amounts tr+tr td {
            border-top: 1px solid #8aa6b2;
        }

        .label {
            color: #52616b;
            display: block;
            font-size: 8px;
            margin-bottom: 1mm;
            text-transform: uppercase;
        }

        .amount {
            font-size: 13px;
            font-weight: bold;
        }

        .warning {
            background: #fff5f5;
            border: 1px solid #d99;
            color: #842029;
            font-size: 8px;
            line-height: 1.4;
            /* margin-top: 3mm; */
            padding: 2mm;
        }

        .acknowledgment {
            line-height: 1.2;
            margin-top: 1mm;
            font-size: 13px;
            text-align: center;
        }

        .receipt-signatures {
            margin-top: 3mm;
        }

        .receipt-signatures td {
            text-align: center;
            vertical-align: top;
            width: 50%;
        }

        .receipt-signatures tr+tr td {
            padding-top: 3mm;
        }

        .signature-space {
            height: 2mm;
        }

        .signature-label {
            border-top: 1px solid #263746;
            display: block;
            padding-top: 2mm;
        }

        .signer {
            font-weight: bold;
            margin-top: 2mm;
        }

        .card-frame {
            border: 1px solid #8aa6b2;
            height: 25mm;
            margin: 2mm auto 0;
            padding: 1mm;
            width: 30mm;
        }

        .card {
            display: block;
            height: 25mm;
            margin: auto;
            max-width: 30mm;
        }
    </style>
</head>
@php
$logoPath = 'assets/images/entete.png';
$devisedepot = infoParRekete("SELECT * FROM caisses WHERE idadresse = '" . $transfert->iddepart . "'", 'devise');
$devisedestination = infoParRekete("SELECT * FROM caisses WHERE idadresse = '" . $transfert->iddestination . "'", 'devise');
$montantVerse = $module === 'recuretrait'
? ($transfert->montpaye ?? $transfert->montretrait)
: $transfert->montretrait;
$transfertRetire = !empty($transfert->datepayement);
$nomSignataire = $transfertRetire
? ($transfert->recepteur ?: '-')
: ($transfert->expediteur ?: '-');
$libelleSignataire = $transfertRetire ? 'récepteur' : 'expéditeur';
$carte = $transfertRetire ? $transfert->carte_recepteur : $transfert->cartexpediteur;
$photoPath = $carte ? 'storage/images/Acteurs/' . $carte : null;
$locationDate = ($transfert->villedepart ?: '-') . ' le ' .
($transfert->datepayement ? date('d/m/Y H:i', strtotime($transfert->datepayement)) : date('d/m/Y H:i'));
$agent = Auth::user();
@endphp
@php
$logoFile = public_path($logoPath);
$logoSource = 'data:image/png;base64,' . base64_encode(file_get_contents($logoFile));
$photoFile = $photoPath ? public_path($photoPath) : null;
$photoInfo = $photoFile && is_file($photoFile) ? getimagesize($photoFile) : false;
$photoSource = $photoInfo
? 'data:' . $photoInfo['mime'] . ';base64,' . base64_encode(file_get_contents($photoFile))
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
                        <!-- <td class="location"</td> -->
                    </tr>
                </table>

                <table class="title-row">
                    <tr>
                        <td>
                            <div class="brand">PAIEMENT FOURNISSEUR</div>
                            <div class="code">Référence : {{ $transfert->code ?: '-' }}</div>
                        </td>
                        <td style="width: 32%;">
                            <div class="location">{{ $locationDate }}</div>
                            <div class="copy">{{ $copie }}</div>
                        </td>
                    </tr>
                </table>

                <div class="statement">
                    La Société Global Vision reconnaît avoir reçu des mains de
                    <span class="value">{{ $transfert->expediteur ?: '-' }}</span>.
                </div>

                <table class="amounts">
                    <tr>
                        <td>
                            <span class="label">Montant déposé</span>
                            <span class="amount">{{ number_format((float) $transfert->montdepot, 0, ',', ' ') }} {{ $devisedepot }}</span>
                        </td>
                        <td>
                            <span class="label">Équivalent à remettre</span>
                            <span class="amount">{{ number_format((float) $montantVerse, 0, ',', ' ') }} {{ $devisedestination }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="label">Bénéficiaire</span>
                            <span class="value">{{ $transfert->recepteur ?: '-' }}</span>
                        </td>
                    </tr>
                </table>

                @if($module == 'recudepot')
                <div class="warning">
                    <strong>Important :</strong> Nous n'avons aucune relation de collaboration avec votre fournisseur
                    (correspondant) et ne sommes pas responsables de l'exécution de votre commande ni des dommages éventuels.
                </div>
                @endif

                <div class="acknowledgment">                   
                    NB: Une fois vos bagages disponibles au magasin, vous serez immédiatement informé par appel et/ou message. Vous
                    disposerez ainsi de 5 jours au maximum pour amener la totalité de votre facture et ramasser vos bagages. Passé ce
                    délai de 5 jours, vous serez facturé de 2000F CFA par CBM et par jour. <br>
                    <b style="color: red;"> Apres 20 jours nous ne serons plus responsable de tout ce qui arriverait à votre bagage . </b>
                    <br>
                La présente décharge est établie pour servir et valoir ce que de droit.
                </div>

                <table class="receipt-signatures">
                    <tr>
                        <td>
                            <div class="signature-space"></div><span class="signature-label">Signature de l'{{ $libelleSignataire }} <br><br><br></span>
                        </td>

                        <td>
                            <div class="signature-space"></div><span class="signature-label">Signature et cachet du caissier(ère)</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="signer">{{ $nomSignataire }}</div>
                            <div class="card-frame">
                                @if($photoSource)
                                <img class="card" src="{{ $photoSource }}" alt="Pièce d'identité">
                                @else
                                <span class="label">Carte non disponible</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="signer">{{ $agent ? $agent->name : '-' }}</div>
                        </td>
                    </tr>
                </table>
            </td>
            @endforeach
        </tr>
    </table>
</body>

</html>