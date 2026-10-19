@extends('layoutback', [])
@section('content')

@php
    $id = isset($lenregistrement) ? $lenregistrement->id : 0;
@endphp

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ $id == 0 ? 'Ajouter un utilisateur' : 'Modifier l\'utilisateur' }}</h6>
    </div>
    <div class="card-body">
        <!-- <form action="{{ url('enregistrer-utilisateur') }}" method="POST">
            @csrf -->
            <input type="hidden" name="idenreg" value="{{ $id }}">

            <div class="row">
                <div class="col-md-4 form-group mb-3">
                    <label>Nom de l'utilisateur <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $lenregistrement->name ?? '') }}" required>
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label>Login <span class="text-danger">*</span></label>
                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $lenregistrement->username ?? '') }}" required>
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $lenregistrement->email ?? '') }}" required>
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label>Mot de passe {!! $id == 0 ? '<span class="text-danger">*</span>' : '<small class="text-muted">(Laissez vide pour conserver l\'actuel)</small>' !!}</label>
                    <input type="password" name="password" id="password" class="form-control" {{ $id == 0 ? 'required' : '' }}>
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label>Profil <span class="text-danger">*</span></label>
                      @php
                        echo chargerCombo($roles, 'id', 'name', 'idrole', 'Choisir un profil', '', "", old('idrole', $lenregistrement->idrole ?? '') );
                        @endphp
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label for="idcaisse">Caisse</label>
                    @php
                        echo chargerCombo($caisses, 'id', 'libelle', 'idcaisse', 'Choisir une caisse', '', '', old('idcaisse', $lenregistrement->idcaisse ?? ''));
                    @endphp
                </div>
                
                <div class="col-md-4 form-group mb-3 mt-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="actif" name="actif" value="1" {{ (!isset($lenregistrement) || $lenregistrement->actif) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="actif">Compte Actif</label>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="button" onclick="valider( 'enregistrer-utilisateur' );" class="btn btn-success btn-lg"><i class="fas fa-save"></i> &nbsp; Enregistrer</button>
                <!-- <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button> -->
                <a href="{{ route('utilisateur') }}" class="btn btn-secondary"><i class="fas fa-times"></i> &nbsp; Annuler</a>
            </div>
        <!-- </form> -->
    </div>
</div>

@endsection
