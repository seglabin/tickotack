<div class="modal fade" id="modalResetPassword" tabindex="-1" aria-labelledby="modalResetPasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalResetPasswordLabel">Réinitialiser le mot de passe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formReinitialiser" action="{{ route('reinitialiser-mot-de-passe') }}" method="POST">
                @csrf
                <input type="hidden" name="iduser" id="reset_iduser">
                <div class="modal-body">
                    <p>Réinitialisation du mot de passe pour l'utilisateur sélectionné.</p>
                    <div class="mb-3">
                        <label for="reset_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="reset_password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="reset_password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="reset_password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">&nbsp; Annuler</button>
                   <button type="button" onclick="validerReinitialiser();" class="btn btn-success btn-lg"><i class="fas fa-save"></i> &nbsp; Réinitialiser le mot de passe</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function validerReinitialiser() { //alert('Merci Seigneur');
        
        document.forms["formReinitialiser"].submit();        

    }
</script>