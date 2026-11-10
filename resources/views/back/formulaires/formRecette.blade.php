<div class="row">
    @php
        $u = Auth::user();
        $idcaisse = old('idcaisse', $a->idcaisse ?? ($u->idcaisse ?? ''));
        $disabled = ($u && (int) $u->idrole === 1) ? '' : 'disabled';
    @endphp

    <div class="col-md-6">
        <div class="card-body">
            <div class="form-group">
                <label for="libelle">Libellé (*)</label>
                <input type="text" class="form-control" id="libelle" name="libelle"
                    onkeyup="enMajuscule('libelle', 1);" placeholder="Entrer le libellé"
                    value="{{ old('libelle', $a->libelle ?? '') }}">
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="montantrecette" class="lelabel">Montant (*)</label>
                <input type="text" class="form-control" id="montantrecette" name="montantrecette"
                    onkeyup="typemontant(this);" placeholder="Ex : 1 000 000"
                    value="{{ old('montantrecette', $a->montantrecette ?? '') }}">
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="daterecette" class="lelabel">Date de la recette (*)</label>
                <input type="datetime-local" class="form-control" id="daterecette" name="daterecette"
                    value="{{ old('daterecette', isset($a) ? \Carbon\Carbon::parse($a->daterecette)->format('Y-m-d\TH:i') : date('Y-m-d\TH:i')) }}">
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="idcaisse" class="lelabel">Caisse (*)</label>
                @php
                    echo chargerCombo($caisses, 'id', 'libelle', 'idcaisse', 'Choisir une caisse', '', '', $idcaisse, $disabled);
                @endphp
            </div>
        </div>
    </div>
</div>
