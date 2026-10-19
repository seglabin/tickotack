<div class="row">
    {{-- `nom`, `tel`, `whatsap`, `marque`, `carte`, `idadreslivr` --}}
@php
$photo = ($a != null) ? $a->carte : '';
$photo = ($a != null && $a->carte != null) ? 'storage/images/Acteurs/' . $a->carte : null;

@endphp
    <div class="col-md-6">
        <div class="card-body">
            <div class="form-group">
                <label for="nom" class="lelabel">Nom complet (*)</label>
                <input type="text" class="form-control" id="nom" name="nom"
                    onkeyup="enMajuscule('nom');"
                    placeholder="Entrer le nom complet de l'acteur"
                    value="{{ ($a != null) ? $a->nom : null }}">
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="tel" class="lelabel">Téléphone (*)</label>
                <input type="text" class="form-control" id="tel" name="tel"
                    onkeyup="typetelephone(this);"
                    placeholder="Ex : 06 00 00 00 00"
                    value="{{ ($a != null) ? $a->tel : null }}">
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-body">
            <div class="form-group">
                <label for="whatsap" class="lelabel">WhatsApp</label>
                <input type="text" class="form-control" id="whatsap" name="whatsap"
                    onkeyup="typetelephone(this);"
                    placeholder="Ex : 06 00 00 00 00"
                    value="{{ ($a != null) ? $a->whatsap : null }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="idadreslivr" class="lelabel">Adresse de livraison</label>
                @php
                $idadreslivr = ($a != null) ? $a->idadreslivr : '';
                echo chargerCombo($adresses, 'id', 'libelle', 'idadreslivr', 'Choisir une adresse', '', '', $idadreslivr);
                @endphp
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="marque" class="lelabel">Marque / Repère</label>
                <textarea class="form-control" id="marque" name="marque" rows="3"
                    placeholder="Description ou repère permettant d'identifier l'acteur">{{ ($a != null) ? $a->marque : null }}</textarea>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-body">
            <div class="form-group">
                <label for="carte" class="lelabel">Photo de la carte / pièce</label>
               <input type="hidden" id="oldcarte" name="oldcarte" value="{{ $photo }}">
                <div class="control-group ">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="  fileupload-preview thumbnail " style="">
                            <img src="{{ asset($photo)}}" />
                        </div>
                        <div>
                            <?php if ($photo != '') { ?>
                                <span class="btn btn-file btn-small btn-warning"><span class="fileupload-new"><i
                                            class="fa fa-camera icon-white"></i></span>
                                <?php    } else { ?>
                                    <span class="btn btn-file btn-small btn-success" title="Choisir"><span
                                            class="fileupload-new"><i class="fa fa-camera"></i></span>
                                    <?php    } ?>
                                    <span class="fileupload-exists btn-small btn-warning" title="Changer"><i
                                            class="fa fa-camera"></i></span><input type="file" name="carte"
                                        id="photo"
                                        value="{{($a != null) ? ($a->{'photo'}) : null}}" /></span>
                                    <span class="btn btn-small btn-danger " data-dismiss="fileupload" title="Supprimer"> <i
                                            class="fa fa-trash"></i></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


</div>