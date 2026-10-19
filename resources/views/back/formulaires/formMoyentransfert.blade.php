<div class="row">
    <!-- `libelle`, `numero` -->
    <div class="col-md-7">
        <div class="card-body">
            <div class="form-group">
                <label for="libelle">Libellé (*) </label>
                <input type="text" class="form-control" onkeyup="enMajuscule('libelle',1);" id="libelle" name="libelle" placeholder="Entrer le libellé du moyen de transfert"
                    value="{{($a != null) ? $a->libelle : null}}">
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card-body">
            <div class="form-group">
                <label for="numero">Numéro / Contact </label>
                <input type="text" class="form-control" id="numero" name="numero" placeholder="Entrer le numéro associé"
                    value="{{($a != null) ? $a->numero : null}}">
            </div>
        </div>
    </div>
</div>
