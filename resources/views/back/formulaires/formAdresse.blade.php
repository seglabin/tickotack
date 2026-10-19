<div class="row">
    <!-- `libelle`, `pays`, `devise` -->
    <div class="col-md-5">
        <div class="card-body">
            <div class="form-group">
                <label for="libelle">Ville / Libellé (*)</label>
                <input type="text" class="form-control" onkeyup="enMajuscule('libelle',1);" id="libelle" name="libelle"
                    placeholder="Entrer la ville ou le libellé"
                    value="{{($a != null) ? $a->libelle : null}}">
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="pays">Pays (*)</label>
                <input type="text" class="form-control" onkeyup="enMajuscule('pays',1);" id="pays" name="pays"
                    placeholder="Entrer le pays"
                    value="{{($a != null) ? $a->pays : null}}">
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
