<div class="row">
    <div class="col-md-6">
        <div class="card-body">
            <div class="form-group">
                <label for="idcaissesortie" class="lelabel">Caisse de sortie (*)</label>
                @php
                    $idcaissesortie = $admin
                        ? old('idcaissesortie', $a->idcaissesortie ?? '')
                        : ($caisseSortieRattachee->id ?? '');
                @endphp
                @if($admin)
                    {!! chargerCombo($caisses, 'id', 'libelle', 'idcaissesortie', 'Choisir la caisse de sortie', '', '', $idcaissesortie) !!}
                @else
                    <input type="text" class="form-control" id="idcaissesortie-affichage"
                        value="{{ $caisseSortieRattachee->libelle ?? 'Aucune caisse rattachée' }}" readonly>
                    <input type="hidden" name="idcaissesortie" value="{{ $idcaissesortie }}">
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-body">
            <div class="form-group">
                <label for="idcaissentree" class="lelabel">Caisse d’entrée (*)</label>
                @php
                    $idcaissentree = old('idcaissentree', $a->idcaissentree ?? '');
                    echo chargerCombo($caisses, 'id', 'libelle', 'idcaissentree', 'Choisir la caisse d’entrée', '', '', $idcaissentree);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="datemvt" class="lelabel">Date du mouvement (*)</label>
                <input type="datetime-local" class="form-control" id="datemvt" name="datemvt"
                    value="{{ old('datemvt', isset($a) ? \Illuminate\Support\Carbon::parse($a->datemvt)->format('Y-m-d\TH:i') : date('Y-m-d\TH:i')) }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="montantsortie" class="lelabel">Montant de sortie (*)</label>
                <input type="text" class="form-control" id="montantsortie" name="montantsortie"
                    onkeyup="typemontant(this);" placeholder="Ex : 1 000 000"
                    value="{{ old('montantsortie', $a->montantsortie ?? '') }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="montantentree" class="lelabel">Montant d’entrée (*)</label>
                <input type="text" class="form-control" id="montantentree" name="montantentree"
                    onkeyup="typemontant(this);" placeholder="Ex : 1 000 000"
                    value="{{ old('montantentree', $a->montantentree ?? '') }}">
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card-body">
            <div class="form-group">
                <label for="observation" class="lelabel">Observation</label>
                <textarea class="form-control" id="observation" name="observation" rows="2"
                    placeholder="Entrer l'observation">{{ old('observation', $a->observation ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>
