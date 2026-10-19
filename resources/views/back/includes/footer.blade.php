<div class="footer-area">
    <div class="footer-top pt-80 pb-40" style="background: #1a1f2c; color: #fff;">
        <div class="container">
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-6 mb-4">
                    <div class="footer-widget">
                        <div class="fw-logo mb-25">
                            <h3 class="text-white fw-bold">SIGAF</h3>
                        </div>
                        <div class="footer-content">
                            <p class="text-white-50">Système Informatique de Gestion Améliorée des Finances. Une solution moderne pour la transparence et l'efficacité financière.</p>
                            <div class="footer-social mt-20">
                                <ul class="list-wrap d-flex gap-3">
                                    <li><a href="javascript:void(0)" class="text-white opacity-75"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="javascript:void(0)" class="text-white opacity-75"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="javascript:void(0)" class="text-white opacity-75"><i class="fab fa-linkedin-in"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4 col-lg-4 col-md-6 mb-4 ps-lg-5">
                    <div class="footer-widget">
                        <h4 class="fw-title text-white mb-30">Liens Utiles</h4>
                        <div class="footer-link-list">
                            <ul class="list-wrap">
                                <li class="mb-2"><a href="{{-- route('accueil') --}}#" class="text-white-50 hover-white"><i class="fas fa-chevron-right me-2 small"></i> Tableau de Bord</a></li>
                                <li class="mb-2"><a href="{{-- route('operation') --}}" class="text-white-50 hover-white"><i class="fas fa-chevron-right me-2 small"></i> Opérations</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h4 class="fw-title text-white mb-30">Contact</h4>
                        <div class="footer-info-list">
                            <ul class="list-wrap">
                                <li class="d-flex align-items-center mb-3 text-white-50">
                                    <div class="icon me-3 text-primary"><i class="fas fa-university"></i></div>
                                    <div class="content">GLOBAL VISION</div>
                                </li>
                                <li class="d-flex align-items-center mb-3 text-white-50">
                                    <div class="icon me-3 text-primary"><i class="fas fa-envelope"></i></div>
                                    <div class="content">contact@sigaf.org</div>
                                </li>
                                <li class="d-flex align-items-center text-white-50">
                                    <div class="icon me-3 text-primary"><i class="fas fa-clock"></i></div>
                                    <div class="content">Lun - Ven: 08:00 - 17:00</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom py-3" style="background: #141822; border-top: 1px solid rgba(255,255,255,0.05);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="copyright-text">
                        <p class="mb-0 text-white-50 small">Copyright © {{ date('Y') }} <span class="text-white">SIGAF</span> | Tous droits réservés.</p>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <ul class="list-wrap d-flex justify-content-md-end gap-3 mb-0 small">
                        <li><a href="#" class="text-white-50">Sécurité</a></li>
                        <li><a href="#" class="text-white-50">Confidentialité</a></li>
                        <li><a href="#" class="text-white-50">Support</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-white:hover {
        color: #fff !important;
        padding-left: 5px;
        transition: all 0.3s ease;
    }
    .footer-widget .fw-title {
        position: relative;
        padding-bottom: 15px;
    }
    .footer-widget .fw-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 2px;
        background: #4e73df;
    }
</style>