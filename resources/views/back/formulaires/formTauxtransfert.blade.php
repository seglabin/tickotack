<div class="row">
    {{--`taux`, `iddepart`, `iddestination`--}}
    @php

    @endphp


    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="iddepart" class="lelabel">Depart (*)</label>
                @php
                $iddepart = ($a != null) ? $a->iddepart : '';
                echo chargerCombo($departs, 'id', 'libelle', 'iddepart', 'Choisir un départ', '', '', $iddepart);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="iddestination" class="lelabel">Destination (*)</label>
                @php
                $iddestination = ($a != null) ? $a->iddestination : '';
                echo chargerCombo($destinations, 'id', 'libelle', 'iddestination', 'Choisir une destination', '', '', $iddestination);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card-body">
            <div class="form-group">
                <label for="taux" class="lelabel">Taux (*)</label>
                <input type="text" class="form-control" id="taux" name="taux"
                    onkeyup="typenumerique(this);"
                    placeholder="Ex : 120"
                    value="{{ ($a != null) ? $a->taux : null }}">
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card-body">
            <div class="form-group">
                <label for="operateur" class="lelabel">Opérateur (*)</label>
                <input type="text" class="form-control" id="operateur" name="operateur"                    
                    placeholder="Ex : *"
                    value="{{ ($a != null) ? $a->operateur : null }}">
            </div>
        </div>
    </div>

</div>
<!-- 
</div>
</div>
</div>

</div> -->