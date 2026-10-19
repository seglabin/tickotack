<div class="row">
    <!-- `libelle`, `pays`, `devise` -->
     <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="idadresse" class="lelabel">Ville de la caisse (*)</label>
                @php
                $idadresse = ($a != null) ? $a->idadresse : '';
                echo chargerCombo($adresses, 'id', 'libelle', 'idadresse', 'Choisir la ville', '', '', $idadresse);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card-body">
            <div class="form-group">
                <label for="libelle">Libellé (*)</label>
                <input type="text" class="form-control" onkeyup="enMajuscule('libelle',1);" id="libelle" name="libelle"
                    placeholder="Entrer la ville ou le libellé"
                    value="{{($a != null) ? $a->libelle : null}}">
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="devise">Devise</label>
                <input type="text" class="form-control" onkeyup="enMajuscule('devise',1);" id="devise" name="devise"
                    placeholder="Ex : XAF, EUR, USD"
                    value="{{($a != null) ? $a->devise : null}}">
            </div>
        </div>
    </div>
</div>
