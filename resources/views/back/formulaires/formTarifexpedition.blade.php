<div class="row">
    {{--`poids`, `prix`, `idtypcolis`, `idtypenvoi`, `idmoyentransport`, `iddepart`, `iddestination`--}}
    @php
   
    @endphp

    
    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="idmoyentransport" class="lelabel">Moyen de transport (*)</label>
                @php
                $idmoyentransport = ($a != null) ? $a->idmoyentransport : '';
                echo chargerCombo($moyens, 'id', 'libelle', 'idmoyentransport', 'Choisir un moyen de transport', '', '', $idmoyentransport);
                @endphp
            </div>
        </div>
    </div>

    
    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="idtypcolis" class="lelabel">Type de colis (*)</label>
                @php
                $idtypcolis = ($a != null) ? $a->idtypcolis : '';
                echo chargerCombo($typecolis, 'id', 'libelle', 'idtypcolis', 'Choisir un type de colis', '', '', $idtypcolis);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="idtypenvoi" class="lelabel">Type d'envoi (*)</label>
                @php
                $idtypenvoi = ($a != null) ? $a->idtypenvoi : '';
                echo chargerCombo($typenvois, 'id', 'libelle', 'idtypenvoi', 'Choisir un type d\'envoi', '', '', $idtypenvoi);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="iddepart" class="lelabel">Ville de départ</label>
                @php
                $iddepart = ($a != null) ? $a->iddepart : '';
                echo chargerCombo($adresses, 'id', 'libelle', 'iddepart', 'Choisir le départ', '', "verifierTrajetTarif('iddepart')", $iddepart);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="iddestination" class="lelabel">Ville de destination</label>
                @php
                $iddestination = ($a != null) ? $a->iddestination : '';
                echo chargerCombo($adresses, 'id', 'libelle', 'iddestination', 'Choisir la destination', '', "verifierTrajetTarif('iddestination')", $iddestination);
                @endphp
            </div>
        </div>
    </div>

    
    <!-- <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="poids" class="lelabel">Poids (*)</label>
                <input type="text" class="form-control" id="poids" name="poids"
                    onkeyup="typenumerique(this);"
                    placeholder="Ex : 1 000 000"
                    value="{{ ($a != null) ? $a->poids : null }}">
            </div>
        </div>
    </div> -->


    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="prix" class="lelabel">Prix (*)</label>
                <input type="text" class="form-control" id="prix" name="prix"
                    onkeyup="typemontant(this);"
                    placeholder="Ex : 1 000 000"
                    value="{{ ($a != null) ? $a->prix : null }}">
            </div>
        </div>
    </div>

</div>

<script>
    function verifierTrajetTarif(champModifie) {
        const depart = document.getElementById('iddepart');
        const destination = document.getElementById('iddestination');

        if (depart.value !== '0' && destination.value !== '0' && depart.value === destination.value) {
            alert('La ville de départ doit être différente de la ville de destination.');
            const champ = document.getElementById(champModifie);
            champ.value = '0';
            $(champ).trigger('change.select2');
        }
    }
</script>