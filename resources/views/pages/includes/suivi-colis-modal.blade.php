<div class="modal fade" id="modalSuiviColis" tabindex="-1" role="dialog" aria-labelledby="modalSuiviColisLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalSuiviColisLabel">Suivi des colis</h4>
      </div>
      <div class="modal-body">
        <form id="formSuiviColis">
          <div class="form-group">
            <label for="suiviCode">N° colis</label>
            <input type="text" class="form-control" id="suiviCode" name="code" placeholder="Ex : EXP-ABC123" maxlength="255" required autofocus>
          </div>
          <button type="submit" class="btn btn-primary btn-block" id="btnSuiviColis">Rechercher</button>
        </form>
        <div id="suiviColisMessage" class="alert" style="display:none; margin-top:15px;"></div>
        <div id="suiviColisResultat" style="display:none; margin-top:20px;"></div>
      </div>
    </div>
  </div>
</div>

<script>
  (function ($) {
    var modal = $('#modalSuiviColis');
    var form = $('#formSuiviColis');
    var button = $('#btnSuiviColis');
    var message = $('#suiviColisMessage');
    var result = $('#suiviColisResultat');

    function escapeHtml(value) {
      return $('<div>').text(value || '-').html();
    }

    function renderContainerLink(container) {
      if (!container.lienconteneur) return '-';

      try {
        var url = new URL(container.lienconteneur, window.location.origin);
        if (url.protocol !== 'http:' && url.protocol !== 'https:') return '-';
        return '<a href="' + escapeHtml(url.href) + '" target="_blank" rel="noopener noreferrer">' +
          escapeHtml(container.conteneur) + '</a>';
      } catch (error) {
        return escapeHtml(container.conteneur);
      }
    }

    function showMessage(text, type) {
      message.removeClass('alert-success alert-danger')
        .addClass('alert-' + type)
        .text(text)
        .show();
    }

    function renderResult(data) {
      var expedition = data.expedition;
      var history = data.historique || [];
      var historyHtml = history.length ? '<ul class="list-unstyled">' : '<p>Aucun historique disponible.</p>';

      history.forEach(function (item) {
        historyHtml += '<li><strong>' + escapeHtml(item.datetat) + '</strong> - ' + escapeHtml(item.etat) + (item.reference ? ' (' + escapeHtml(item.reference) + ')' : '') + '</li>';
      });
      if (history.length) historyHtml += '</ul>';

      result.html(
        '<h4>Expédition ' + escapeHtml(expedition.code) + '</h4>' +
        '<dl class="dl-horizontal">' +
        '<dt>Statut</dt><dd>' + escapeHtml(expedition.etat) + '</dd>' +
        '<dt>Date</dt><dd>' + escapeHtml(expedition.datenreg) + '</dd>' +
        '<dt>Colis</dt><dd>' + escapeHtml(expedition.nbrcolis) + '</dd>' +
        '<dt>Poids</dt><dd>' + escapeHtml(expedition.poids) + '</dd>' +
        '<dt>Départ</dt><dd>' + escapeHtml(expedition.depart) + '</dd>' +
        '<dt>Destination</dt><dd>' + escapeHtml(expedition.destination) + '</dd>' +
        '<dt>Expéditeur</dt><dd>' + escapeHtml(expedition.expediteur) + '</dd>' +
        '<dt>Récepteur</dt><dd>' + escapeHtml(expedition.recepteur) + '</dd>' +
        '<dt>Conteneur</dt><dd>' + renderContainerLink(expedition) + '</dd>' +
        '</dl>' +
        '<h4>Historique</h4>' + historyHtml
      ).show();
    }

    form.on('submit', function (event) {
      event.preventDefault();
      button.prop('disabled', true).text('Recherche...');
      message.hide();
      result.hide().empty();

      $.get('{{ route('suivi-colis.recherche') }}', form.serialize())
        .done(function (data) {
          renderResult(data);
        })
        .fail(function (xhr) {
          showMessage(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Une erreur est survenue.', 'danger');
        })
        .always(function () {
          button.prop('disabled', false).text('Rechercher');
        });
    });

    @if(request()->boolean('suivi'))
      modal.modal('show');
    @endif
  })(jQuery);
</script>
