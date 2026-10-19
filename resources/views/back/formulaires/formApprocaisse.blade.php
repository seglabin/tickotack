<div class="row">
    {{-- `dateappro`, `montant`, `observation`, `idsource`, `idcaisse`--}}
    @php
    $u = Auth::user();
     $idcaisse = ($a != null) ? $a->idcaisse : (($u != null) ? $u->idcaisse : '');
    $disabled = ($u != null && $u->idrole == 1) ? '' : 'disabled';
     @endphp

     
         <div class="col-md-4">
             <div class="card-body">
                 <div class="form-group">
                     <label for="idcaisse" class="lelabel">Caisse (*)</label>
                     @php                
                     echo chargerCombo($caisses, 'id', 'libelle', 'idcaisse', 'Choisir une caisse', '', '', $idcaisse,$disabled);
                     @endphp
                 </div>
             </div>
         </div>
    
    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="idsource" class="lelabel">Source d'approvisionnement (*)</label>
                @php
                $idsource = ($a != null) ? $a->idsource : '';
                echo chargerCombo($sourcesele, 'id', 'libelle', 'idsource', 'Choisir une source', '', '', $idsource);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="dateappro" class="lelabel">Date d'approvisionnement (*)</label>
                <input type="datetime-local" class="form-control" id="dateappro" name="dateappro"
                    onkeyup="enMajuscule('dateappro');"
                    placeholder="Entrer la date d'approvisionnement"
                    value="{{ ($a != null) ? $a->dateappro : date('Y-m-d\TH:i') }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="montant" class="lelabel">Montant (*)</label>
                <input type="text" class="form-control" id="montant" name="montant"
                    onkeyup="typemontant(this);"
                    placeholder="Ex : 1 000 000"
                    value="{{ ($a != null) ? $a->montant : null }}">
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card-body">
            <div class="form-group">
                <label for="observation" class="lelabel">Observation</label>
                <textarea class="form-control" id="observation" name="observation" rows="2"
                    placeholder="Entrer l'observation"
                    value="{{ ($a != null) ? $a->observation : null }}"></textarea>
            </div>
        </div>
    </div>

</div>