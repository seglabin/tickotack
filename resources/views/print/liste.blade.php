<!DOCTYPE html>
<html lang="fr">
@php
$labonne = $labonne ?? null;
$img = ($labonne != null && $labonne->logo != null) ? $labonne->logo : null;
$libabonne = ($labonne != null) ? $labonne->designation : 'GLOBAL VISION';
//donneeimprim coltitre coldata
$titres = $coltitre != '' ? explode('|', $coltitre) : array();
$cols = $coldata != '' ? explode('|', $coldata) : array();

//dd($cols);

//dd($img);
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $letitre }}</title>

    <!-- Panel         -->
    <link href="{{ asset('assets/temleApexa/css/stylepanel.css')}}" rel="stylesheet">


    <link href="{{ public_path('assets/temleApexa/css/bootstrap.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ public_path('assets/temleApexa/css/pdf.css') }}" type="text/css">
    <style>
        .centrerGrille {
            display: grid;
            place-items: center;
        }

        .tableauto {
            width: 100%;
            table-layout: auto;
            border-collapse: collapse;
        }

        .L {
            text-align: left;
        }

        .R {
            text-align: right;
        }

        .C {
            text-align: center;
        }

        #letablo {
            border-collapse: collapse;
            color: #243447;
            font-size: 10pt;
            margin-top: 12px;
            table-layout: auto;
            width: 100%;
        }

        #letablo thead {
            display: table-header-group;
        }

        #letablo th {
            background-color: #1f4e78;
            border: 1px solid #cbd5e1;
            color: #fff;
            font-weight: bold;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        #letablo td {
            border: 1px solid #cbd5e1;
            padding: 7px;
            vertical-align: top;
        }

        #letablo tbody tr:nth-child(even) {
            background-color: #f1f5f9;
        }

        #letablo tr {
            page-break-inside: avoid;
        }

        .print-header {
            border-collapse: collapse;
            margin-bottom: 8px;
            width: 100%;
        }

        .print-header td {
            vertical-align: middle;
        }

        .print-header__logo {
            display: block;
            /* justify-content: center;  /* horizontal /
            align-items: center;  vertical */
            margin: auto 20% auto 20%;
            max-width: 100%;
            width: 60%;
            /* max-height: 150px; */
            object-fit: contain;

            /* optionnel, pour le responsive */
            /* height: auto;
            max-height: 75px;
            max-width: 395px;
            width: auto; */
        }

        /* 
        .print-header__logo {
            height: auto;
            max-height: 75px;
            max-width: 395px;
            object-fit: contain;
            width: auto;
        } */

        .print-header__brand {
            text-align: center;
        }

        .print-header__brand h3 {
            color: #1f4e78;
            font-size: 17pt;
            margin: 0;
        }

        .print-header__title {
            font-size: 13pt;
            font-weight: bold;
            margin: 4px 0;
        }

        .print-header__date {
            color: #52616b;
            font-size: 9pt;
            margin: 0;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 100px;
            text-align: center;
            line-height: 35px;
        }

        footer:after {

            /* counter-increment: page; */
            /* content:"Page " counter(page) " / " ; */
            counter-reset: page 1;
            /* counter-increment: page; */
            content: "Page " counter(page) " / " counter(pages);

        }
    </style>

</head>

<body>


    <table class="bartitre print-header">
        <tr>
            <td style="width: 100%;">
                @if($img != null)
                <img class="logo print-header__logo" src="{{ public_path('storage/images/abonnements/' . $img)}}" alt="Logo de l’organisation">
                @else
                <img class="logo print-header__logo" src="{{ public_path('assets/images/entete.png') }}" alt="Global Vision">
                @endif
            </td>

        </tr>
        <!-- </table>
    <table class="bartitre print-header"> style="width: 70%;" -->
        <tr>
            <td class="print-header__brand">
                <!-- <h3>{{ $libabonne }}</h3> -->
                <div class="print-header__title">{{ $letitre }}</div>
                <p class="print-header__date">Imprimé le {{ now()->format('d/m/Y à H:i') }}</p>
            </td>
        </tr>
    </table>

    <hr class="trait">

    @if(count($hautdoc) > 0)
    <table class="tableauto" style="font-size:2rem;">
        @foreach($hautdoc as $ln)
        <tr>
            @foreach ($ln as $i => $e)
            <td class="L">
                @if($i % 2 == 0)
                <b style="margin-right:none;">{{ $e }}</b>
                @else
                {{ $e }}
                @endif
            </td>
            @endforeach
        </tr>

        @endforeach
    </table>
    @endif

    <!-- Saut de page -->
    <!-- <div style="page-break-after: always;" ></div> -->
    <br>

    <table id="letablo" class=" impression table table-bordered table-striped  " scrol>
        <thead>
            <tr>
                @foreach($titres as $t)
                <th>{{ $t }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($donneeimprim as $i => $d)
            <tr class="items">
                @foreach($cols as $c)
                <td>{{ $d[$c] }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    @php
    // dd($config);
    @endphp
    @if($config =='details-composition' && isset($donneestats) && $donneestats != null)
    <!-- <div class="page-break-after"></div> -->
    <br><br>
    <div>
        @php
        $tot = $donneestats->nbG + $donneestats->nbF;
        $pourcF = $donneestats->nbFcompo > 0 ? round((($donneestats->nbFmoy / $donneestats->nbFcompo) * 100),2) : 0;
        $pourcG = $donneestats->nbGcompo > 0 ? round((($donneestats->nbGmoy / $donneestats->nbGcompo) * 100),2) : 0;
        $pourc = ($donneestats->nbGcompo + $donneestats->nbFcompo) > 0 ? round(((($donneestats->nbGmoy + $donneestats->nbFmoy) / ($donneestats->nbGcompo + $donneestats->nbFcompo)) * 100),2) : 0;
        @endphp

        <h4><b>Cotonou le {{ $donneestats->datecompo }}</b></h4>
        <h4><b> {{ $donneestats->libelle }}</b></h4>
        <h4>Statistiques de la composition</h4>
        <br>
        <table class="tableauto table table-bordered table-striped" style="font-size:1.2rem; width:50%;">
            <tr>
                <th>Cours : </th>
                <td>{{ $laclannesco->libclasabrev }}</td>
            </tr>

            <tr>
                <th>Elément</th>
                <th>Filles</th>
                <th>Garçons</th>
                <th>Total</th>
            </tr>
            <tr>
                <th>Effectifs</th>
                <td>{{ $donneestats->nbF }}</td>
                <td>{{ $donneestats->nbG }}</td>
                <td>{{ ($donneestats->nbG + $donneestats->nbF) }}</td>
            </tr>
            <tr>
                <th>Ont Composé</th>
                <td>{{ $donneestats->nbFcompo }}</td>
                <td>{{ $donneestats->nbGcompo }}</td>
                <td>{{ ($donneestats->nbGcompo + $donneestats->nbFcompo) }}</td>
            </tr>
            <tr>
                <th>Ont obtenu la moyenne</th>
                <td>{{ $donneestats->nbFmoy }}</td>
                <td>{{ $donneestats->nbGmoy }}</td>
                <td>{{ ($donneestats->nbGmoy + $donneestats->nbFmoy) }}</td>
            </tr>
            <tr>
                <th>Pourcentage d'admis</th>
                <td>{{ $pourcF.'%' }}</td>
                <td>{{ $pourcG.'%' }}</td>
                <td>{{ $pourc.'%' }}</td>
            </tr>
        </table>

        <h3>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            LA DIRECTRICE
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            CHARGE DE COURS
        </h3>

        <br>
        <br>
        <br>
        <br>
        <br>

        <h4>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Soeur Chantal KOSSOU &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Viviane GNONHOUE
        </h4>
    </div>


    <div class="row">
        <div>

        </div>

    </div>
    @endif
    <!-- <footer> 
        <hr class="traitPied">
        Mon pied de page 
    </footer> -->
    <style>
        @page {
            @if(in_array(session('module'), ['etats-colis', 'etats-transferts'], true)) size: A4 landscape;
            @elseif(in_array(session('module'), ['etats-paiements', 'etats-depenses', 'etats-recettes'], true)) size: A4 portrait;
            @endif margin: 10mm 10mm 10mm 10mm;
            /* Ajustez les marges pour laisser de la place au pied de page */
        }

        #footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9pt;
        }

        #footer .page-number:before {
            content: "Page : ";
        }

        #footer .page-number:after {

            content: counter(page);
            /*"  sur " counter(Page); */
        }
    </style>

    <!-- Dans le corps du document -->
    <div id="footer">
        <span class="page-number"> </span>
    </div>
    @php


    // if (isset($pdf)) {
    // if ($PAGE_COUNT > 1) {
    // $font = $fontMetrics->get_font('Arial, Helvetica, sans-serif', 'normal');
    // $size = 12;
    // $pageText = 'Page ' . $PAGE_NUM . ' sur ' . $PAGE_COUNT;
    // $y = 15;
    // $x = 520;
    // $pdf->text($x, $y, $pageText, $font, $size);
    // }
    // }
    @endphp

    @include('back.includes.js')

</body>

</html>