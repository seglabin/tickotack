<div class="tg-header__top">
				<div class="container custom-container">
					<div class="row">
						<div class="col-lg-6">
							<ul class="tg-header__top-info left-side list-wrap">
								<li>SIGAF</li>
								<li>Système Informatique de Gestion Améliorée des Finances</li>
							</ul>
						</div>
						<div class="col-lg-6">
							<ul class="tg-header__top-right list-wrap">
								<li>GLOBAL VISION</li>
								<li id="real-time-clock"><i class="fas fa-clock"></i> Chargement...</li>
							</ul>
						</div>
					</div>
				</div>
			</div>

<script>
    function updateClock() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            day: 'numeric', 
            month: 'long', 
            year: 'numeric',
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        };
        const clockElement = document.getElementById('real-time-clock');
        if (clockElement) {
            clockElement.innerHTML = '<i class="fas fa-clock"></i> ' + now.toLocaleDateString('fr-FR', options);
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>