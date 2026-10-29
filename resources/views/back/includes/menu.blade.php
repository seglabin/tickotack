                        @php
                        $menusUser = session('menusUser');
                        $config = session('config');
                        $module = session('module')!= null?session('module'):'';
                        $tparam = ['element', 'moyentransferts', 'adresse', 'acteur', 'caisse', 'tauxtransfert', 'tarifexpedition', 'article', 'categorie', 'marque', 'moyenpaiement', 'typelivraison'];
                        $toperation = ['approcaisse', 'mvtcaisse', 'depense', 'transfert', 'expedition', 'conteneur'];
                      
						@endphp

                        <div class="col-12">
                        	<div class="tgmenu__wrap">
                        		<nav class="tgmenu__nav">
                        			<div class="logo">
                        				<a href="{{ route('home') }}"><img src="{{ asset('assets/templeApexa/img/logo/logo.png')}}" alt="Logo" /></a>
                        			</div>
                        			<div class="tgmenu__navbar-wrap tgmenu__main-menu d-none d-lg-flex">
                        				<ul class="navigation">
                                                @if((int) (auth()->user()->idrole ?? 0) === 1)
                                                <li class="{{ request()->routeIs('admin.historique-actions') ? 'active' : '' }}"><a href="{{ route('admin.historique-actions') }}">Historique des actions</a></li>
                                                @endif
                        					@if(is_array($menusUser) && in_array(1, $menusUser))
                        					<li class=" {{ in_array(session('config'), $toperation) ? 'active' : '' }}  menu-item-has-children">
                        						<a href="#">Opérations courrantes</a>
                        						<ul class="sub-menu">
                        							@if(is_array($menusUser) && in_array(2, $menusUser))<li class="{{ ($config == 'approcaisse' && $module == 'approcaisse') ? 'active' : '' }}"><a href="{{ url('approcaisse') }}">Appro caisse</a></li>@endif
                                                @if(is_array($menusUser) && in_array(35, $menusUser))<li class="{{ ($config == 'mvtcaisse' && $module == 'mvtcaisse') ? 'active' : '' }}"><a href="{{ url('mvtcaisse') }}">Transactions entre caisse</a></li>@endif
                        							@if(is_array($menusUser) && in_array(3, $menusUser))<li class="{{ ($config == 'depense' && $module == 'depense') ? 'active' : '' }}"><a href="{{ url('depense') }}">Depense</a></li>@endif
												@if(is_array($menusUser) && (in_array(4, $menusUser) || in_array(5, $menusUser)))
												<li class="{{ in_array($config, ['expedition', 'conteneur']) ? 'active' : '' }} menu-item-has-children">
													<a href="#">Expédition</a>
													<ul class="sub-menu">
														@if(in_array(4, $menusUser))<li class="{{ ($config == 'expedition' && $module == 'expedition') ? 'active' : '' }}"><a href="{{ url('expedition') }}">Enregistrement colis</a></li>@endif
														@if(in_array(5, $menusUser))<li class="{{ ($config == 'conteneur' && $module == 'conteneur') ? 'active' : '' }}"><a href="{{ url('conteneur') }}">Enregistrement expéditions</a></li>@endif
														@if(in_array(4, $menusUser))<li class="{{ ($config == 'expedition' && $module == 'retraitcolis') ? 'active' : '' }}"><a href="{{ url('retrait-colis') }}">Retrait colis</a></li>@endif
													</ul>
												</li>
												@endif
												@if(is_array($menusUser) && in_array(6, $menusUser))
												<li class="{{ $config == 'transfert' ? 'active' : '' }} menu-item-has-children">
													<a href="#">Transfert</a>
													<ul class="sub-menu">
														<li class="{{ ($config == 'transfert' && $module == 'transfert') ? 'active' : '' }}"><a href="{{ url('transfert') }}">Enregistrement transfert</a></li>
														<li class="{{ ($config == 'transfert' && $module == 'retraittransfert') ? 'active' : '' }}"><a href="{{ url('retrait-transfert') }}">Retrait transfert</a></li>
													</ul>
												</li>
												@endif
                        						</ul>
                        					</li>
                        					@endif
                        					@if(is_array($menusUser) && in_array(7, $menusUser))
                        					<li class=" {{ in_array(session('config'), $tparam) ? 'active' : '' }}  menu-item-has-children">
                        						<a href="#">Paramètres</a>
                        						<ul class="sub-menu">
                        							@if(is_array($menusUser) && in_array(8, $menusUser))
                        							<li class=" {{ in_array(session('config'), ['element', 'moyentransferts']) ? 'active' : '' }} menu-item-has-children">
                        								<a href="#">Paramètres généraux</a>
                        								<ul class="sub-menu">
                        									@if(is_array($menusUser) && in_array(11, $menusUser))<li class="{{ ($config == 'element' && $module == 'consignataire') ? 'active' : '' }}"><a href="{{ url('consignataire') }}">Consignataire</a></li>@endif
                        									@if(is_array($menusUser) && in_array(12, $menusUser))<li class="{{ ($config == 'element' && $module == 'typecolis') ? 'active' : '' }}"><a href="{{ url('typecolis') }}">Type de colis</a></li>@endif
                        									@if(is_array($menusUser) && in_array(13, $menusUser))<li class="{{ ($config == 'element' && $module == 'moyentransport') ? 'active' : '' }}"><a href="{{ url('moyentransport') }}">Moyen de transport</a></li>@endif
                        									@if(is_array($menusUser) && in_array(14, $menusUser))<li class="{{ ($config == 'element' && $module == 'etat') ? 'active' : '' }}"><a href="{{ url('etat') }}">Etat</a></li>@endif
                        									@if(is_array($menusUser) && in_array(15, $menusUser))<li class="{{ ($config == 'element' && $module == 'sourceappro') ? 'active' : '' }}"><a href="{{ url('sourceappro') }}">Source d'appro</a></li>@endif
                        									@if(is_array($menusUser) && in_array(16, $menusUser))<li class="{{ ($config == 'element' && $module == 'typenvoi') ? 'active' : '' }}"><a href="{{ url('typenvoi') }}">Type d'envoi</a></li>@endif
                        									@if(is_array($menusUser) && in_array(17, $menusUser))<li class="{{ ($config == 'moyentransferts' && $module == 'moyentransferts') ? 'active' : '' }}"><a href="{{ url('moyentransferts') }}">Moyen de transfert</a></li>@endif
                        								</ul>
                        							</li>
                        							@endif
                        							@if(is_array($menusUser) && in_array(9, $menusUser))
                        							<li class=" {{ in_array($config, ['adresse', 'acteur', 'caisse', 'tauxtransfert', 'tarifexpedition', 'element']) ? 'active' : '' }} menu-item-has-children">
                        								<a href="#">Configuration</a>
                        								<ul class="sub-menu">
                        									@if(is_array($menusUser) && in_array(18, $menusUser))<li class="{{ ($config == 'element' && $module == 'articledepense') ? 'active' : '' }}"><a href="{{ url('articledepense') }}">Article de dépense</a></li>@endif
                        									@if(is_array($menusUser) && in_array(19, $menusUser))<li class="{{ ($config == 'adresse' && $module == 'adresses') ? 'active' : '' }}"><a href="{{ url('adresse') }}">Adresses / Villes</a></li>@endif
                        									@if(is_array($menusUser) && in_array(20, $menusUser))<li class="{{ ($config == 'acteur' && $module == 'acteurs') ? 'active' : '' }}"><a href="{{ url('acteur') }}">Acteurs</a></li>@endif
                        									@if(is_array($menusUser) && in_array(21, $menusUser))<li class="{{ ($config == 'caisse' && $module == 'caisse') ? 'active' : '' }}"><a href="{{ url('caisse') }}">Caisse</a></li>@endif
                        									@if(is_array($menusUser) && in_array(22, $menusUser))<li class="{{ ($config == 'tauxtransfert' && $module == 'tauxtransfert') ? 'active' : '' }}"><a href="{{ url('tauxtransfert') }}">Taux de change</a></li>@endif
                        									@if(is_array($menusUser) && in_array(23, $menusUser))<li class="{{ ($config == 'tarifexpedition' && $module == 'tarifexpedition') ? 'active' : '' }}"><a href="{{ url('tarifexpedition') }}">Tarif d'expédition</a></li>@endif
                        									@if(is_array($menusUser) && in_array(26, $menusUser))<li class="{{ ($config == 'marque' && $module == 'marque') ? 'active' : '' }}"><a href="{{ url('marque') }}">Marques</a></li>@endif
                        									@if(is_array($menusUser) && in_array(27, $menusUser))<li class="{{ ($config == 'moyenpaiement' && $module == 'moyenpaiement') ? 'active' : '' }}"><a href="{{ url('moyenpaiement') }}">Moyen de paiement</a></li>@endif
                        									@if(is_array($menusUser) && in_array(28, $menusUser))<li class="{{ ($config == 'typelivraison' && $module == 'typelivraison') ? 'active' : '' }}"><a href="{{ url('typelivraison') }}">Type de livraison</a></li>@endif
                        								</ul>
                        							</li>
                        							@endif
                        							@if(is_array($menusUser) && in_array(10, $menusUser))
                        							<li class=" {{ in_array($config, ['article', 'categorie']) ? 'active' : '' }} menu-item-has-children">
                        								<a href="#">Paramètres e-commerce</a>
                        								<ul class="sub-menu">
                        									@if(is_array($menusUser) && in_array(24, $menusUser))<li class="{{ ($config == 'article' && $module == 'article') ? 'active' : '' }}"><a href="{{ url('article') }}">Articles</a></li>@endif
                        									@if(is_array($menusUser) && in_array(25, $menusUser))<li class="{{ ($config == 'categorie' && $module == 'categorie') ? 'active' : '' }}"><a href="{{ url('categorie') }}">Catégories</a></li>@endif
                        								</ul>
                        							</li>
                        							@endif
                        				</ul>
                        				@endif
                        			</div>
                        			<div class="tgmenu__action d-none d-md-block">
                        				<ul class="list-wrap" style="display:flex;align-items:center;gap:10px;">

                        					<!-- Accès rapide Nouveau / Retour  -->
                        					@php
                        					if(!isset($nouv)) $nouv = null;
                        					if(!isset($retour)) $retour = null;
                        					@endphp

                        					@if ($nouv != null)
                        					<li style="list-style:none;">
                        						<button type="button" class="btn btnarrondi m-b-10 btn-outline-primary"
                        							onclick="nouveau('{{$nouv}}');"><i class="fa fa-plus-circle"></i>
                        							&nbsp; Nouveau</button>
                        					</li>
                        					@endif

											<li style="list-style:none;">
                        						<button type="button" class="btn btnarrondi m-b-10 btn-outline-primary"
                        							onclick="afficher('site');"><i class="fa fa-arrow"></i>
                        							&nbsp; Site</button>
                        					</li>

                        					{{--<li style="list-style:none;">
                        						<a href="{{ route('home') }}">Site</a>
                        					</li>--}}
                        					@if ($retour != null)
                        					<li style="list-style:none;">
                        						<button type="button" class="btn btnarrondi m-b-10 btn-outline-primary"
                        							onclick="afficher('{{$retour}}');"><i class="fa fa-arrow-left"></i>
                        							&nbsp; Retour</button>
                        					</li>
                        					@endif

                        					<!-- Menu Utilisateur -->
                        					@include('back.includes.menuAuth')

                        				</ul>
                        			</div>
                        <button type="button" class="mobile-nav-toggler" aria-label="Ouvrir le menu" aria-controls="mobile-menu" aria-expanded="false">
                        				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18" fill="none">
                        					<path d="M0 2C0 0.895431 0.895431 0 2 0C3.10457 0 4 0.895431 4 2C4 3.10457 3.10457 4 2 4C0.895431 4 0 3.10457 0 2Z" fill="currentcolor" />
                        					<path d="M0 9C0 7.89543 0.895431 7 2 7C3.10457 7 4 7.89543 4 9C4 10.1046 3.10457 11 2 11C0.895431 11 0 10.1046 0 9Z" fill="currentcolor" />
                        					<path d="M0 16C0 14.8954 0.895431 14 2 14C3.10457 14 4 14.8954 4 16C4 17.1046 3.10457 18 2 18C0.895431 18 0 17.1046 0 16Z" fill="currentcolor" />
                        					<path d="M7 2C7 0.895431 7.89543 0 9 0C10.1046 0 11 0.895431 11 2C11 3.10457 10.1046 4 9 4C7.89543 4 7 3.10457 7 2Z" fill="currentcolor" />
                        					<path d="M7 9C7 7.89543 7.89543 7 9 7C10.1046 7 11 7.89543 11 9C11 10.1046 10.1046 11 9 11C7.89543 11 7 10.1046 7 9Z" fill="currentcolor" />
                        					<path d="M7 16C7 14.8954 7.89543 14 9 14C10.1046 14 11 14.8954 11 16C11 17.1046 10.1046 18 9 18C7.89543 18 7 17.1046 7 16Z" fill="currentcolor" />
                        					<path d="M14 2C14 0.895431 14.8954 0 16 0C17.1046 0 18 0.895431 18 2C18 3.10457 17.1046 4 16 4C14.8954 4 14 3.10457 14 2Z" fill="currentcolor" />
                        					<path d="M14 9C14 7.89543 14.8954 7 16 7C17.1046 7 18 7.89543 18 9C18 10.1046 17.1046 11 16 11C14.8954 11 14 10.1046 14 9Z" fill="currentcolor" />
                        					<path d="M14 16C14 14.8954 14.8954 14 16 14C17.1046 14 18 14.8954 18 16C18 17.1046 17.1046 18 16 18C14.8954 18 14 17.1046 14 16Z" fill="currentcolor" />
                        				</svg>
                        </button>
                        		</nav>
                        	</div>
                        	<!-- Mobile Menu  -->
                        	<div class="tgmobile__menu">
                                <nav class="tgmobile__menu-box" id="mobile-menu" aria-label="Navigation principale">
                                    <button type="button" class="close-btn" aria-label="Fermer le menu"><i class="fas fa-times"></i></button>
                        			<div class="nav-logo">
                        				<a href="{{ route('home') }}"><img src="{{ asset('assets/templeApexa/img/logo/logo.png') }}" alt="Logo" /></a>
                        			</div>
									<ul class="navigation back-mobile-site-link">
										<li>
											<a href="{{ url('site') }}"><i class="fa fa-globe" aria-hidden="true"></i> Site</a>
										</li>
									</ul>
									<ul class="navigation back-mobile-user-menu" aria-label="Menu utilisateur">
										@include('back.includes.menuAuth')
									</ul>
                        			<div class="tgmobile__menu-outer">
                        				<!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->
                        			</div>
                        		</nav>
                        	</div>
                        	<div class="tgmobile__menu-backdrop"></div>
                        	<!-- End Mobile Menu -->
                        </div>

                        <style>
                        	.user-dropdown-item:hover {
                        		background: #f3f4f6 !important;
                        	}

                        	.user-logout-btn:hover {
                        		background: #fee2e2 !important;
                        	}

                        .user-menu-toggle:hover {
                        		background: rgba(255, 255, 255, 0.2) !important;
                        	}
                        </style>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var toggles = document.querySelectorAll('.user-menu-toggle');

                                function closeUserMenu(toggle) {
                                    var dropdown = toggle.parentElement.querySelector('.user-dropdown-menu');
                                    var chevron = toggle.querySelector('.fa-chevron-down');
                                    dropdown.style.display = 'none';
                                    toggle.setAttribute('aria-expanded', 'false');
                                    if (chevron) {
                                        chevron.style.transform = 'rotate(0deg)';
                                    }
                                }

                                toggles.forEach(function(toggle) {
                                    var dropdown = toggle.parentElement.querySelector('.user-dropdown-menu');
                                    toggle.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        var isVisible = dropdown.style.display === 'block';
                                        dropdown.style.display = isVisible ? 'none' : 'block';
                                        toggle.setAttribute('aria-expanded', isVisible ? 'false' : 'true');
                                        var chevron = toggle.querySelector('.fa-chevron-down');
                                        if (chevron) {
                                            chevron.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
                                        }
                                    });
                                });

                                document.addEventListener('click', function(e) {
                                    toggles.forEach(function(toggle) {
                                        if (!toggle.parentElement.contains(e.target)) {
                                            closeUserMenu(toggle);
                                        }
                                    });
                                });
                            });
                        </script>