@extends('layoutback', [])
@section('content')

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            @if($module == 'listeProfil')
                Gestion des droits d'accès par Rôle
            @elseif($module == 'adddroitacces')
                Ajout de permissions au rôle : {{ $lenregistrement->name ?? '' }}
            @elseif($module == 'detailsdroitacces')
                Retrait de permissions au rôle : {{ $lenregistrement->name ?? '' }}
            @endif
        </h6>
    </div>
    <div class="card-body">
        
        @if($module == 'listeProfil')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Rôle</th>
                            <th>Code</th>
                            <th width="300px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($donnees as $i => $role)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->code }}</td>
                            <td>
                                <a href="{{ route('rolepermission', ['module' => 'adddroitacces', 'idenreg' => $role->id]) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus"></i> &nbsp; Accorder
                                </a>
                                <a href="{{ route('rolepermission', ['module' => 'detailsdroitacces', 'idenreg' => $role->id]) }}" class="btn btn-sm btn-danger">
                                    <i class="fas fa-minus"></i> &nbsp; Retirer
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @else
            <!-- Formulaire d'ajout ou de retrait de permissions -->
            <!-- <form action="{{-- route('valider-droits-acces') --}}" method="GET"> -->
                <input type="hidden" name="module" value="{{ $module == 'adddroitacces' ? 'adddroitacces' : 'retraitdroitacces' }}">
                <input type="hidden" name="idenreg" value="{{ $idenreg }}">
                <input type="hidden" name="taille" value="{{ count($donnees) }}">

                @if(count($donnees) == 0)
                    <div class="alert alert-info">
                        Aucune permission disponible pour cette action.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th width="50px">Cocher</th>
                                    <th>Permissions</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($donnees as $i => $perm)
                                    @php
                                        $isChild = $perm->idparent != null;
                                    @endphp
                                    <tr class="{{ $isChild ? 'bg-light' : '' }}">
                                        <td class="text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input perm-check" 
                                                       id="chk{{$i}}" name="chk{{$i}}" value="1"
                                                       data-id="{{ $perm->id }}"
                                                       data-parent="{{ $perm->idparent ?? '' }}">
                                                <label class="custom-control-label" for="chk{{$i}}"></label>
                                            </div>
                                            <input type="hidden" name="idpermission{{$i}}" value="{{ $perm->id }}">
                                        </td>
                                        <td style="{{ $isChild ? 'padding-left: 40px;' : 'font-weight: bold;' }}">
                                            @if($isChild) 
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-muted mr-2"></i> 
                                            @endif
                                            <label for="chk{{$i}}" style="cursor: pointer; width: 100%;">{{ $perm->libelle }}</label>
                                        </td>
                                        <td>{{ $perm->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                
                <div class="form-group mt-4">
                    @if(count($donnees) > 0)
                        <button type="button" onclick="afficher( 'valider-droits-acces');" class="btn btn-success btn-lg"><i class="fas fa-save"></i> &nbsp; Valider la sélection</button>
                        <!-- <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> &nbsp; Valider la sélection</button> -->
                    @endif
                    <a href="{{ route('rolepermission') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> &nbsp; Retour aux rôles</a>
                </div>
            <!-- </form> -->

        @endif

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checks = document.querySelectorAll('.perm-check');
    
    checks.forEach(check => {
        check.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const parentId = this.getAttribute('data-parent');
            
            if (this.checked) {
                // Si on coche un enfant, on coche automatiquement le parent
                if (parentId) {
                    const parentCheck = document.querySelector(`.perm-check[data-id="${parentId}"]`);
                    if (parentCheck && !parentCheck.checked) {
                        parentCheck.checked = true;
                    }
                }
            } else {
                // Si on décoche un parent, on décoche tous les enfants
                const children = document.querySelectorAll(`.perm-check[data-parent="${id}"]`);
                children.forEach(child => {
                    if (child.checked) {
                        child.checked = false;
                    }
                });
            }
        });
    });
});
</script>

@endsection
