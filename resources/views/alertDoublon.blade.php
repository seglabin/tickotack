@extends('layoutback', ['titre' => $titre ?? 'ALERTE'])

@section('content')
<div class="card shadow-sm border-0 rounded-4 my-4 p-4 text-center">
    <div class="my-4">
        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3.5rem;"></i>
    </div>
    <h4 class="text-danger fw-bold mb-3">{{ $titre ?? 'Attention' }}</h4>
    <p class="fs-5 text-muted mb-4">{!! nl2br(e($info ?? 'Une erreur est survenue.')) !!}</p>
    <div class="pb-3">
        <a href="javascript:history.back()" class="btn btn-outline-primary btnarrondi px-4 py-2">
            <i class="fa fa-arrow-left me-2"></i> Retour
        </a>
    </div>
</div>
@endsection
