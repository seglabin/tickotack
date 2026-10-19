<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Reçu {{ $transfert->code }}</title>
    <style>
        @page {
            size: A5;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #17202a;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .toolbar {
            padding: 10px 0;
            text-align: center;
        }

        .logoentete {
            width: 100%;
            height: auto;
        }

        .carteacteur {
            width: 40%;
            height: auto;
        }

        .toolbar button {
            background: #1f6feb;
            border: 0;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            padding: 8px 18px;
        }

        .receipt {
            border: 1px solid #263746;
            height: 132mm;
            margin-bottom: 6mm;
            padding: 8mm;
            position: relative;
        }

        .receipt:last-child {
            margin-bottom: 0;
        }

        .header {
            border-bottom: 2px solid #263746;
            display: flex;
            justify-content: space-between;
            margin-bottom: 5mm;
            padding-bottom: 3mm;
        }

        .brand {
            font-size: 17px;
            font-weight: bold;
        }

        .copy {
            color: #52616b;
            font-size: 10px;
            text-transform: uppercase;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 4mm;
            text-align: center;
            text-transform: uppercase;
        }

        .code {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .grid {
            display: grid;
            gap: 3mm 7mm;
            grid-template-columns: 1fr 1fr;
            margin-top: 5mm;
        }

        .field {
            border-bottom: 1px solid #bdc3c7;
            min-height: 10mm;
            padding-bottom: 1mm;
        }

        .label {
            color: #52616b;
            display: block;
            font-size: 9px;
            margin-bottom: 1mm;
            text-transform: uppercase;
        }

        .value {
            font-size: 12px;
            font-weight: 600;
        }

        .amount {
            background: #eef5f7;
            border: 1px solid #8aa6b2;
            font-size: 16px;
            grid-column: 1 / -1;
            padding: 4mm;
            text-align: center;
        }

        .signatures {
            bottom: 7mm;
            display: grid;
            gap: 8mm;
            grid-template-columns: 1fr 1fr;
            left: 8mm;
            position: absolute;
            right: 8mm;
        }

        .signature {
            border-top: 1px solid #263746;
            padding-top: 2mm;
            text-align: center;
        }

        .small {
            color: #52616b;
            font-size: 9px;
        }

        @media print {
            .toolbar {
                display: none;
            }

            .receipt {
                break-inside: avoid;
            }
        }

        .avertissement {
            color: red;
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>
@php

$logoPath = 'assets/images/entete.png';
//$logoPath = 'storage/images/entete.png';
//$b = file_exists($logoPath);
//dd($b);
//dd($module);
$devisedepot = infoParRekete("SELECT * FROM caisses WHERE idadresse = '" . $transfert->iddepart . "'", 'devise');  

$devisedestination = infoParRekete("SELECT * FROM caisses WHERE idadresse = '" . $transfert->iddestination . "'", 'devise'); 
$montantVerse = $module === 'recuretrait'
    ? ($transfert->montpaye ?? $transfert->montretrait)
    : $transfert->montretrait;


@endphp

<body>
    <!-- <div class="toolbar"><button type="button" onclick="window.print()">Imprimer / Enregistrer en PDF</button></div> -->
    <div class="toolbar"> </div>

    @foreach (['Exemplaire client', 'Exemplaire caisse'] as $copie)

    @php
    $loc = $transfert->villedepart ?: '-';
    $loc .=' le ';
    $loc .= $transfert->datepayement ? date('d/m/Y H:i', strtotime($transfert->datepayement)) : date('d/m/Y H:i');
    $u = Auth::user();
    @endphp
    <section class="receipt">

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <img class="logoentete " src="{{ public_path($logoPath)}}" alt="KK">

                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;"><span class="value">{{ $loc}}</span>
                </td>
            </tr>
        </table>

        <div class="header">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 70%; vertical-align: top;">
                        <div class="brand">PAYEMENT FOURNISSEUR</div>
                    </td>
                    <td style="width: 30%; vertical-align: top; text-align: right;">
                        <div class="copy">{{ $copie }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- <h1>Confirmation de retrait</h1> -->
        <p>La Société Global Vision reconnait avoir reçu des mains de : <span class="value">{{ $transfert->expediteur ?: '-' }}</span>
            <br>
            La somme de : <span class="value">{{ number_format((float) $transfert->montdepot, 0, ',', ' ') }} </span> {{ $devisedepot }}
            <br>
            Equivalent de : <span class="value">{{ number_format((float) $montantVerse, 0, ',', ' ') }} </span> {{ $devisedestination }}
            A remettre à son Fournisseur Mr/Mme <span class="value">{{ $transfert->recepteur ?: '-' }}</span>
            <br>
        <div class="amount">
            <div class="avertissement">
                @if($module =='recudepot')
                <span>NB : </span>Nous vous informons que nous n'avons aucune relation de collaboration
                avec votre fournisseur (corespondant) et nous nous désolidarisons de la non exécution de votre
                commande. Nous ne sommes en aucun cas impliqués, ni responsable des dommages qui pourraient arriver et déclinons toutes responsabilité à ce titre
            @endif
            </div>

            <div>
                En foi de quoi la présente décharge est établie pour servir et valoir ce que de droit
            </div>
        </div>

        </p>
        <!-- <div class="code">N° {{ $transfert->code }}</div>
        <div class="grid">
            <div class="field"><span class="label">Date du transfert</span><span class="value">{{ $transfert->datetransfert ? date('d/m/Y H:i', strtotime($transfert->datetransfert)) : '-' }}</span></div>
            <div class="field"><span class="label">Date du retrait</span><span class="value">{{ $transfert->datepayement ? date('d/m/Y H:i', strtotime($transfert->datepayement)) : date('d/m/Y H:i') }}</span></div>
            <div class="field"><span class="label">Expéditeur</span><span class="value">{{ $transfert->expediteur ?: '-' }}</span></div>
            <div class="field"><span class="label">Récepteur</span><span class="value">{{ $transfert->recepteur ?: '-' }}</span></div>
            <div class="field"><span class="label">Départ</span><span class="value">{{ $transfert->villedepart ?: '-' }}</span></div>
            <div class="field"><span class="label">Destination</span><span class="value">{{ $transfert->villedestination ?: '-' }}</span></div>
            <div class="field"><span class="label">Moyen de transfert</span><span class="value">{{ $transfert->moyen ?: '-' }}</span></div>
            <div class="field"><span class="label">Caisse</span><span class="value">{{ $transfert->caisse ?: '-' }}</span></div>
            <div class="amount"><span class="label">Montant reçu</span>{{ number_format((float) $transfert->montretrait, 0, ',', ' ') }}</div>
        </div> -->
        <br>
        <table style="width: 100%; ">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class=""><span class="value">Signature et numéro du {{($module =='recudepot')?'déposant':'récepteur'}} </span></div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <div class=""><span class="value">Signature et cachet du caissier(ère)</span></div>
                </td>
            </tr>
            <tr>
                <td style="height: 30px;"> </td>
            </tr>
            <tr>
                <td style="width: 50%; vertical-align: top; text-align: center;">
                    <div class=""><span class="value">{{ ($module =='recudepot')? ($transfert->expediteur ?: '-'):($transfert->recepteur ?: '-') }}</span></div>
                    <div>
                    @php 
                    if($module =='recudepot')  $photoPath = 'storage/images/Acteurs/'.$transfert->cartexpediteur;
                    if($module =='recuretrait')  $photoPath = 'storage/images/Acteurs/'.$transfert->carte_recepteur;
                    @endphp
                        <img class="carteacteur " src="{{ public_path($photoPath)}}" alt="acteur">
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: center;">
                    <div class=""><span class="value">{{ $u->name ?: '-' }}</span></div>
                </td>
            </tr>
        </table>
        
    </section>
    <br><br>
    @endforeach
</body>

</html>