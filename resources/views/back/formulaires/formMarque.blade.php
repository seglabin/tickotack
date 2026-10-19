<!-- Slides -->
<div class="row">
    <div class="col-md-10">
        <div class="row">
            <!-- left column  `libelle`, `description`, `photo`, `idparent` -->

            <div class="col-md-6">
                <div class="card-body">
                    <div class="form-group">
                        <label for="libelle">Libellé (*) </label>
                        <input type="text" class="form-control" id="libelle" name="libelle" onkeyup="enMajuscule('libelle',1);" placeholder="Enter le titre"
                            value="{{($a != null) ? $a->libelle : null}}">
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card-body">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" id="description" cols="3"
                            value="{{($a != null) ? $a->description : null}}"
                            placeholder="Enter la description">{{($a != null) ? $a->description : null}}</textarea>
                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <div class="checkbox">
                    <div class="row" style="margin-top:35px;">                        
                        <div class="col-md-4"><input type="checkbox" onchange="valCoche('visible');" {{(($a != null) && ($a->visible == 1)) ? 'checked' : ''}} id="visible" name="visible"
                                value="{{($a != null) ? $a->visible : 0}}"> <label for="visible"> Visible</label>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label><strong>Photo </strong></label>
            <input type="hidden" id="oldphoto" name="oldphoto" value="<?php echo $photo; ?>">
            <div class="control-group ">
                <div class="fileupload fileupload-new" data-provides="fileupload">
                    <div class="  fileupload-preview thumbnail " style="width: 120px; height: 120px;">
                        <img src="{{ asset($photo)}}" />
                    </div>
                    <div>
                        <?php if ($photo != '') { ?>
                            <span class="btn btn-file btn-small btn-warning"><span class="fileupload-new"><i
                                        class="fa fa-camera icon-white"></i></span>
                            <?php } else { ?>
                                <span class="btn btn-file btn-small btn-success" title="Choisir"><span
                                        class="fileupload-new"><i class="fa fa-camera"></i></span>
                                <?php } ?>
                                <span class="fileupload-exists btn-small btn-warning" title="Changer"><i
                                        class="fa fa-camera"></i></span><input type="file" name="photo" id="photo"
                                    value="<?php echo $photo; ?>" /></span>
                                <span class="btn btn-small btn-danger " data-dismiss="fileupload" title="Supprimer"> <i
                                        class="fa fa-trash"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>