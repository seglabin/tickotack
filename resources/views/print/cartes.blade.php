<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartes</title>
    @include('includes.css');
    <link href="{{ public_path('assets/css/bootstrap.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ public_path('pdf.css') }}" type="text/css">
    <style>
        .bord {
            border: 10px #117a8b double;
            border-radius: 10%;
            height: 300px;
            margin: 4px;
        }

        .cadre {
            border: 5px #117a8b solid;
            border-radius: 2px;
            padding: 2px;
            height: 325px;
            width: 500px;
            margin: 4px;
        }

        .page {
            margin: -90px 0 0 -50px;

        }
        .pagea {
            margin: -90px 0 0 -50px;

        }
        .titretaille{
            font-size: 10px;
        }
    </style>
</head>

<body>
    @php
        $img = ($labonne != null && $labonne->logo != null) ? $labonne->logo : null;
        $lannee = ($lannesco != null && $lannesco->libannee != null) ? $lannesco->libannee : null;

    @endphp

    <div class="page">
        <table>
            @foreach ($donneeimprim as $i => $a)

                @if (($i % 2) == 0)
                    <tr>
                @endif

                    <td>
                        <div class="cadre">
                            <table>
                                <tr>
                                    <td>
                                        @php
                                            $imagePath = 'storage/images/abonnements/' . $img;
                                        @endphp
                                        @if($img != null && is_file($imagePath))
                                            <img class="logo" src="{{ public_path($imagePath)}}" alt="KK">
                                        @else

                                        @endif
                                    </td>
                                    <td>
                                        <div class="col-xs-10 titretaille" style="padding: 2px;">
                                            <center>
                                                <b> REPUBLIQUE DU BENIN</b>
                                                <br>
                                                <b>MINISTERE DES ENSEIGNEMENTS</b>
                                                <br>
                                                <b>PRIMAIRE ET SECONDAIRE - PROFESSIONNEL</b>
                                            </center>
                                        </div>
                                    </td>
                                     <td>
                                       <img class="logo" src="{{ public_path('assets/img/amoarite.png') }}" alt="Amoarite">
                                    </td>
                                </tr>
                                <tr>
                                </tr>
                            </table>
                            <div style="width:100%">
                                <img class="" style="margin-left: -5px; width:102%;"
                                    src="{{ public_path('assets/img/drapeau.png') }}" alt="Amoarite">
                            </div>
                            <table class="texteCarte" style="padding-left: 10px;">
                                <tr>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td class="titreCarte" style="width:80%;">Carte scolaire &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        {{ $lannee }}
                                    </td>
                                    <td rowspan="4">
                                        @php
                                            $photoPath = 'storage/images/Personnes/' . $a['photo'];
                                        @endphp
                                        @if(is_file($photoPath))
                                            <img class="photoPersonne" src="{{ public_path($photoPath)}}" alt="photo">
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Nom :</b> {{ $a['nom'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Préoms : </b> {{ $a['prenoms'] }}</td>
                                </tr>
                                <tr>
                                    <td><b>Classe : </b>{{ $a['classeactuelle'] }} <b>Sexe : </b> {{ $a['sexe'] }} </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <center>
                                            <b>{{ ($labonne != null && $labonne->titredirecteur != null) ? $labonne->titredirecteur : 'Signataire' }}</b>
                                            <br>
                                            <br>
                                            {{ ($labonne != null && $labonne->directeur != null) ? $labonne->directeur : 'Nom signataire' }}
                                        </center>
                                    </td>
                                </tr>

                            </table>

                        </div>
                    </td>
                    @if (($i % 2) != 0)
                        </tr>
                    @endif
            @endforeach
        </table>

    </div>


</body>

</html>