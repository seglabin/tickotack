<div class="row">
    <!-- `libelle`, `nomtable`, `lecas`, `valeur`-->

    <div class="col-md-6">
        <div class="card-body">
            <div class="form-group">
                <label for="libelle">Libellé (*) </label>
                <input type="hidden" id="nomtable" name="nomtable" value="{{session('nomtable')}}">
                <input type="text" class="form-control" onkeyup="enMajuscule('libelle',1);" id="libelle" name="libelle" placeholder="Enter le libellé"
                    value="{{($a != null) ? $a->libelle : null}}">
            </div>
        </div>
    </div>
    @if(in_array(session('nomtable'), ['etat']))
    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="description">Le cas </label>
                <input type="text" class="form-control" id="lecas" name="lecas" placeholder="Enter le cas"
                    value="{{($a != null) ? $a->lecas : null}}">
            </div>
        </div>

    </div>
    <div class="col-md-2">
        <div class="card-body">
            <div class="form-group">
                <label for="description">Valeur </label>
                <input type="text" class="form-control" id="valeur" name="valeur" placeholder="Enter la valeur"
                    value="{{($a != null) ? $a->valeur : null}}">
            </div>
        </div>
    </div>
    @else
    <input type="hidden" id="lecas" name="lecas" value="">
    <input type="hidden" id="valeur" name="valeur" value="">
    @endif

</div>