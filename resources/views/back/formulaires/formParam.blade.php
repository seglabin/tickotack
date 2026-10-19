@php
$id = isset($lenregistrement) ? $lenregistrement->id : 0;
$config = session('config');
$isRole = $config == 'role';
$isPermission = $config == 'permission';
@endphp


<div class="row">
    @if($isRole)
    <div class="col-md-6 form-group mb-3">
        <label>Nom du rôle <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $lenregistrement->name ?? '') }}" required>
    </div>
    <div class="col-md-6 form-group mb-3">
        <label>Code <span class="text-danger">*</span></label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $lenregistrement->code ?? '') }}" required>
    </div>
    @elseif($isPermission)
    <div class="col-md-6 form-group mb-3">
        <label>Parent </label>
        @php
        echo chargerCombo($parents, 'id', 'libelle', 'idparent', 'Choisir un parent', '', "", old('idparent', $lenregistrement->idparent ?? '') );
        @endphp
    </div>
    <div class="col-md-6 form-group mb-3">
        <label>Libellé <span class="text-danger">*</span></label>
        <input type="text" name="libelle" class="form-control" value="{{ old('libelle', $lenregistrement->libelle ?? '') }}" required>
    </div>
    <div class="col-md-12 form-group mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $lenregistrement->description ?? '') }}</textarea>
    </div>
    @endif