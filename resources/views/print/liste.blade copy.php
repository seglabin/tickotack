<!DOCTYPE html>
<html lang="fr">
@php
$img = null;
//$img =($labonne != null && $labonne->logo!= null )?$labonne->logo:null;
//$libabonne =($labonne != null )?$labonne->designation:'TITIRE ABONNE';
//donneeimprim coltitre coldata
$titres = $coltitre != ''? explode('|',$coltitre):array();
$cols = $coldata != ''? explode('|',$coldata):array();

//dd($cols);

//dd($img);
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
   

    <link href="{{ public_path('assets/temleApexa/css/bootstrap.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ public_path('assets/temleApexa/css/pdf.css') }}" type="text/css"> 
    
    <style>
        .centrerGrille {
            display: grid;
            place-items: center;
        }

        .trait {
            border: none;
            height: 3px;
            background-color: brown;
            width: 90%;
            margin: 20px auto;
        }

        .logo {
            width: 90px;
            height: 90px;
            border-radius: 10%;
        }
    </style>

</head>

<body>

    <div class="">
        <div style="width: 10%; float: left;">
            @if($img != null)
            <img class="logo" src="{{ public_path('storage/images/abonnements/'.$img)}}" alt="KK">

            @else

            @endif
        </div>
        <div style="width: 80%; float: left;">
            <center>
                <h3>{{ $libabonne }} </h3>
            </center>
        </div>
        <div style="width: 10%; float: left;">
            <img class="logo" src="{{ public_path('assets/img/amoarite.png') }}" alt="Amoarite">
        </div>
 </div>
 <br>
 <br>
 <br>
 <hr class="trait">
 <div class="row">
            <div class="col-md-2">AAA</div>
            <div class="col-md-8">BBB</div>
            <div class="col-md-2">CCC</div>
        </div>

        <!-- <table>
            <tr>
                <td>
                    @if($img != null)
                    <img class="logo" src="{{ public_path('storage/images/abonnements/'.$img)}}" alt="KK">

                    @else

                    @endif
                </td>
                <td style="width:800px;">
                    <center>
                        <h3>{{ $libabonne }} </h3>
                    </center>
                </td>
                <td>
                    <img class="logo" src="{{ public_path('assets/img/amoarite.png') }}" alt="Amoarite">
                </td>
            </tr>

        </table>
        <div class="row">
            <div class="col-md-2">AAA</div>
            <div class="col-md-8">BBB</div>
            <div class="col-md-2">CCC</div>
        </div>
        <hr class="trait">


        <div style="width: 50%; float: left;">Content for column 2</div>
        <div style="clear: both;"></div> -->

        <h1>Salut Marie!</h1>
        <!-- Saut de page -->
        <!-- <div style="page-break-after: always;" ></div> -->

        Merci Seigneur Jésus
        <br>
        <table id="letablo" class=" products table table-bordered table-striped appliqueDT " scrol>
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


   
    @include('includes.js')
</body>

</html>