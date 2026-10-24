
@php
// lenregistrement
$config = session('config') ? session('config') : null;
$nouv = null;
$retour = $config;
//  dd($config);
switch ($config) {
// case 'slide':
// $titre = ($lenregistrement) ? "Modification d'un slide" : "Ajout d'un slide";
// break;

}

$a = ($lenregistrement) ? $lenregistrement : null;


@endphp
{{--
@extends('layout-conculting', ['titre'=>$titre])--}}
  @extends('layoutback', ['titre'=>$titre])

@section('content')
        <div class="contact__form-wrap">
            <p>Les champs obligatoires sont marqués (*)</p>
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif
                            
        @switch($config)
            
                @case('element')
                @include('back.formulaires.formElement')
                @break

                @case('moyentransferts')
                @include('back.formulaires.formMoyentransfert')
                @break

                @case('adresse')
                @include('back.formulaires.formAdresse')
                @break

                @case('acteur')
                @include('back.formulaires.formActeur')
                @break

                @case('caisse')
                @include('back.formulaires.formCaisse')
                @break
                        
                @case('approcaisse')
                @include('back.formulaires.formApprocaisse')
                @break

                @case('mvtcaisse')
                @include('back.formulaires.formMvtcaisse')
                @break

                @case('depense')
                @include('back.formulaires.formDepense')
                @break  

                @case('tauxtransfert')
                @include('back.formulaires.formTauxtransfert')
                @break  

                @case('tarifexpedition')
                @include('back.formulaires.formTarifexpedition')
                @break  

                @case('transfert')
                @include('back.formulaires.formTransfert')
                @break  

                @case('expedition')
                @include('back.formulaires.formExpedition')
                @break  

                @case('conteneur')
                @include('back.formulaires.formConteneur')
                @break

                @case('role')
                @case('permission')
                @include('back.formulaires.formParam')
                @break  
                
                @case('categorie')
                @include('back.formulaires.formCategorie')
                @break  
                
                @case('marque')
                @include('back.formulaires.formMarque')
                @break  
                
                @case('article')
                @include('back.formulaires.formArticle')
                @break  

                
        @endswitch
<br>
        <div class="">
            <input type="button" class="btn btnarrondi btn-outline-primary" onclick="valider('{{$valider}}','{{$id}}');" value="Valider">
        </div>
    </div> 
@endsection