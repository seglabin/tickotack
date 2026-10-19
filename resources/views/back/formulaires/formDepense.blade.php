<div class="row">
    {{-- `datedepense`, `libelle`, `montantdepense`, `idcaisse`--}}
    @php
    //$idcaissedefaut = ($u != null) ? $u->idcaisse : '';
    $u = Auth::user();
     $idcaisse = ($a != null) ? $a->idcaisse : (($u != null) ? $u->idcaisse : '');
    $disabled = ($u != null && $u->idrole == 1) ? '' : 'disabled';
    @endphp
    
        <div class="col-md-6">
            <div class="card-body">
                <div class="form-group">
                    <label for="    libelle">Libellé (*) </label>                
                    <input type="text" class="form-control" onkeyup="enMajuscule('libelle',1);" id="libelle" name="libelle" placeholder="Enter le libellé"
                        value="{{($a != null) ? $a->libelle : null}}">
                </div>
            </div>
        </div>
    
    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="montantdepense" class="lelabel">Montant (*)</label>
                <input type="text" class="form-control" id="montantdepense" name="montantdepense"
                    onkeyup="typemontant(this);"
                    placeholder="Ex : 1 000 000"
                    value="{{ ($a != null) ? $a->montantdepense : null }}">
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="datedepense" class="lelabel">Date de la dépense (*)</label>
                <input type="datetime-local" class="form-control" id="datedepense" name="datedepense"
                    onkeyup="enMajuscule('datedepense');"
                    placeholder="Entrer la date de la dépense"
                    value="{{ ($a != null) ? $a->datedepense : date('Y-m-d\TH:i') }}">
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="idcaisse" class="lelabel">Caisse (*)</label>
                @php               
                echo chargerCombo($caisses, 'id', 'libelle', 'idcaisse', 'Choisir une caisse', '', '', $idcaisse,$disabled);
                @endphp
            </div>
        </div>
    </div>
    

</div>