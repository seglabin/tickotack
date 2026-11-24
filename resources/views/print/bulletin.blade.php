<!DOCTYPE html>
<html lang="fr">
@php
    $img = ($labonne != null && $labonne->logo != null) ? $labonne->logo : null;
    $libabonne = ($labonne != null) ? $labonne->designation : 'TITIRE ABONNE';
    //donneeimprim coltitre coldata
    $coltitre = '';
    $coldata = '';
    $letitre = '';
    $config = '';
    $titres = $coltitre != '' ? explode('|', $coltitre) : array();
    $cols = $coldata != '' ? explode('|', $coldata) : array();

    //dd($cols);

    //dd($img);

    $donneeimprim = array();
    $hautdoc = array();
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $letitre }}</title>
    @include('includes.css')

    <link href="{{ public_path('assets/css/bootstrap.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ public_path('pdf.css') }}" type="text/css">
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

        .cadresignature{
            border: 1px solid grey;
            width: 300px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            padding: 10px;
            /* display: flex;
            justify-content: center; */
        }
    </style>

</head>

<body>


    <table class="bartitre">
        <tr>
            <td style="width: 10%; float: left;">
                @if($img != null)
                    <img class="logo" src="{{ public_path('storage/images/abonnements/' . $img)}}" alt="KK">

                @else

                @endif
            </td>
            <td style="width: 80%; float: left;">
                <center>
                    <h3>{{ $libabonne }} </h3>
                </center>
            </td>
            <td style="width: 10%; float: left;">
                <img class="logo" src="{{ public_path('assets/img/amoarite.png') }}" alt="Amoarite">
            </td>
        </tr>

    </table>

    <hr class="trait">


    <div class="letitre">
        <table>
            <th style="width: 30%; float: left; font-size: medium;"> {{ $lasession->libelle }}</th>
            <th style="width: 40%; float: left; font-family: 'Stencil Std';" class="C"> Bulletin de notes</th>
            <th style="width: 30%; text-align: right; font-size: medium;"> {{ $lannesco->libannee }}</th>
        </table>

    </div>
    <div>
        <table>
            <th style="width: 60%; float: left; font-size: medium;"> Classe : {{ $laclannesco->libclas }} </th>
            <th style="width: 20%; float: left; font-size: medium;"> Nbre F : {{ $laclannesco->nbF }} </th>
            <th style="width: 20%; float: left; font-size: medium;"> Nbre G : {{ $laclannesco->nbG }} </th>
            <th style="width: 20%; float: left; font-size: medium;"> Effectif :
                {{ ($laclannesco->nbG + $laclannesco->nbF) }}
            </th>
        </table>
    </div>


    <!-- Saut de page -->
    <!-- <div style="page-break-after: always;" ></div> -->
    <br>

    @foreach ($dbulletin as $d)
        @php
            // dd($d);
        @endphp
        <table>
            <th style="width: 80%; float: left; font-size: medium;">Apprenant(e) : {{ $d['libapprenant'] }} </th>
            <th style="width: 40%; text-align: right; font-size: medium;"> EDUCMASTER : {{  $d['educmaster'] }} </th>
        </table>

        <br>
        <table id="letablo" class=" impression table table-bordered table-striped  " scrol>
            <thead>
                <tr>
                    <th>Matières</th>
                    <th>Moy. Int</th>
                    <th>Dev 1</th>
                    <th>Dev 2</th>
                    <th>Moy</th>
                    <th>Coef</th>
                    <th>Moy. coef</th>
                    <th>Appréciation</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $details = $d['details'];
                    // dd($details);
                @endphp
                @foreach ($details as $v)
                    <tr class="items">
                        <td>{{ $v['libmat'] }}</td>
                        <td class="R">{{ ($v['moyinterro'] >= 0) ? $v['moyinterro'] : '' }}</td>
                        <td class="R">{{ ($v['dev1'] >= 0) ? $v['dev1'] : ''  }}</td>
                        <td class="R">{{  ($v['dev2'] >= 0) ? $v['dev2'] : '' }}</td>
                        <td class="R">{{ ($v['m'] >= 0) ? $v['m'] : '' }}</td>
                        <td class="R">{{ ($v['coef'] >= 0) ? $v['coef'] : ''}}</td>
                        <td class="R">{{ ($v['moycoef'] >= 0) ? $v['moycoef'] : '' }}</td>
                        <td>{{ $v['appreciation'] }}</td>
                    </tr>

                @endforeach
            </tbody>
        </table>

        <br>
        <div>
            <table>
                <tr>
                    <td style="width: 40%;">
                        <div>

                        <table  class=" table table-bordered table-striped   " scrol>
                                <thead>
                                    <tr>
                                        <th>Moyenne {{ $lasession->libelle }} </th>                                        
                                        <td>{{ $d['moy'] }} </td>                                        
                                    </tr>
                                    <tr>
                                        <th>Rang</th>                                        
                                        <td>{{ $d['rang']}} </td>                                        
                                    </tr>
                                    <tr>
                                        <th>Mention </th>                                        
                                        <td>{{ $d['appreciation']}} </td>                                        
                                    </tr>
                                    <tr>
                                        <th>Forte Moy. </th>                                        
                                        <td>55 </td>                                        
                                    </tr>
                                    <tr>
                                        <th>Faible Moy. </th>                                        
                                        <td>55 </td>                                        
                                    </tr>
                                </thead>                                
                            </table>                            
                        </div>
                    </td>
                    <td style="width: 20%;"></td>
                    <td style="width: 40%;">
                        <div>
                            <table  class=" table table-bordered table-striped   " scrol>
                                <thead>
                                    <tr>
                                        <th>Moy. 1er Trim </th>                                        
                                        <td>55 </td>                                        
                                    </tr>
                                    <tr>
                                        <th>Moy. 2ème Trim </th>                                        
                                        <td>55 </td>                                        
                                    </tr>
                                    <tr>
                                        <th>Moy. 3ème Trim </th>                                        
                                        <td>55 </td>                                        
                                    </tr>
                                </thead>                                
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- </div>
            <div> -->

        </div>

        <br>
       <div class="cadresignature">
           <h4>{{ $labonne->titredirecteur }}</h4>
           <br><br><br><br><br>
           <h4>{{ $labonne->directeur }}</h4>

       </div>


        <div style="page-break-after: always;"></div>
    @endforeach


    <footer>
        <hr class="traitPied">

    </footer>
    <style>
        @page {
            margin: 10mm 10mm 10mm 10mm;
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
        //     if ($PAGE_COUNT > 1) {
        //         $font = $fontMetrics->get_font('Arial, Helvetica, sans-serif', 'normal');
        //         $size = 12;
        //         $pageText = 'Page ' . $PAGE_NUM . ' sur ' . $PAGE_COUNT;
        //         $y = 15;
        //         $x = 520;
        //         $pdf->text($x, $y, $pageText, $font, $size);
        //     }
        // }
    @endphp

    @include('includes.js')

</body>

</html>