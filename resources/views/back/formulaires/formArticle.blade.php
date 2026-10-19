<!-- Slides -->
<div class="card-body">
    <div class="row">

        <!-- //`libelle`, `reference`, `description`, `photo1`, `photo2`, `photo3`, `photo4`, `photo5`, `lot`, `carousel`, `idparent`, `idcategorie`, `idmarque` -->

        <div class="col-md-4">
            <div class="form-group">
                <label for="idcategorie">Catégorie (*) </label>
                @php
                    $idcategorie = ($a != null) ? $a->idcategorie : old('idcategorie');
                    echo chargerCombo($categories, 'id', 'libelle', 'idcategorie', 'Choisir une categorie', '', '', $idcategorie);
                @endphp
            </div>
        </div>


        <div class="col-md-4">
            <div class="form-group">
                <label for="idmarque">Marque </label>
                @php
                    $idmarque = ($a != null) ? $a->idmarque : old('idmarque');
                    echo chargerCombo($marques, 'id', 'libelle', 'idmarque', 'Choisir une marque', '', '', $idmarque);
                @endphp
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="libelle">Parent (Lot) </label>
                @php
                    $idparent = ($a != null) ? $a->idparent : old('idparent');
                    echo chargerCombo($parents, 'id', 'libelle', 'idparent', 'Choisir un parent', '', '', $idparent);
                @endphp
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="libelle">Libellé (*) </label>
                <input type="text" class="form-control" id="libelle" name="libelle" onkeyup="enMajuscule('libelle',1);"
                    placeholder="Enter le titre" value="{{($a != null) ? $a->libelle : null}}">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="libelle">Référence </label>
                <input type="text" class="form-control" id="reference" name="reference"
                    onkeyup="enMajuscule('reference');" placeholder="Enter la référence"
                    value="{{($a != null) ? $a->reference : null}}">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="libelle">Prix </label>
                <input type="text" class="form-control" id="prix" name="prix"
                    onkeyup="typemontant(this);" placeholder="Enter le prix"
                    value="{{($a != null) ? $a->prix : 0}}">
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" name="description" id="description"
                    onkeyup="enMajuscule('description',1); " cols="3" value="{{($a != null) ? $a->description : null}}"
                    placeholder="Enter la description">{{($a != null) ? $a->description : null}}</textarea>
            </div>
        </div>
        <div class="col-md-3">
            <div class="checkbox">
                <div class="row" style="margin-top:35px;">
                    <div class="col-md-4"><input type="checkbox" onchange="valCoche('lot');" id="lot" {{(($a != null) && ($a->lot == 1)) ? 'checked' : ''}} name="lot" value="{{($a != null) ? $a->lot : 0}}"> <label
                            for="lot">Lot </label></div>
                    <div class="col-md-4"><input type="checkbox" onchange="valCoche('carousel');" {{(($a != null) && ($a->carousel == 1)) ? 'checked' : ''}} id="carousel" name="carousel"
                            value="{{($a != null) ? $a->carousel : 0}}"> <label for="carousel"> Carousel</label></div>
                </div>
            </div>
        </div>
        @for ($i = 1; $i <= 5; $i++)
            @php
                $phot = ${'photo' . $i};
            @endphp
            <div class="col-md-2">
                <div class="form-group">
                    <label><strong>Photo {{ $i }}</strong></label>
                    <input type="hidden" id="oldphoto{{ $i }}" name="oldphoto{{ $i }}" value="{{ $phot }}">
                    <div class="control-group ">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="  fileupload-preview thumbnail " style="width: 120px; height: 120px;">
                                <img src="{{ asset($phot)}}" />
                            </div>
                            <div>
                                <?php    if ($phot != '') { ?>
                                <span class="btn btn-file btn-small btn-warning"><span class="fileupload-new"><i
                                            class="fa fa-camera icon-white"></i></span>
                                    <?php    } else { ?>
                                    <span class="btn btn-file btn-small btn-success" title="Choisir"><span
                                            class="fileupload-new"><i class="fa fa-camera"></i></span>
                                        <?php    } ?>
                                        <span class="fileupload-exists btn-small btn-warning" title="Changer"><i
                                                class="fa fa-camera"></i></span><input type="file" name="photo{{ $i }}"
                                            id="photo{{ $i }}"
                                            value="{{($a != null) ? ($a->{'photo' . $i}) : null}}" /></span>
                                    <span class="btn btn-small btn-danger " data-dismiss="fileupload" title="Supprimer"> <i
                                            class="fa fa-trash"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endfor

    </div>
</div>