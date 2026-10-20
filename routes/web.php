<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     // return view('welcome');
//     return view('back.accueilA');
// })->name('accueil');
// Route::get('/accueil A', function () {
//     return view('back.accueilA');
// });

// Route::get('/accueil B', function () {
//     return view('back.accueilB');
// });
$id = '';

Route::get('/site', function () {
    session(['config' => 'site']);
    return redirect()->route('home');
});

Route::get('/back', function () {
    session(['config' => 'back']);
    return redirect()->intended('/');
    // return redirect()->route('back.accueilA');
});


Route::get('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'authenticate']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);

// Route::get('/', [App\Http\Controllers\LandingController::class, 'index'])->name('landing');


Route::middleware(['auth'])->group(function () {

    Route::get('/typecolis', function () {
        session(['nomtable' => 'typecolis']);
        return redirect()->route('element');
    });

    Route::get('/moyentransport', function () {
        session(['nomtable' => 'moyentransport']);
        return redirect()->route('element');
    });

    Route::get('/etat', function () {
        session(['nomtable' => 'etat']);
        return redirect()->route('element');
    });

    Route::get('/   sourceappro', function () {
        session(['nomtable' => 'sourceappro']);
        return redirect()->route('element');
    });

    Route::get('/typenvoi', function () {
        session(['nomtable' => 'typenvoi']);
        return redirect()->route('element');
    });

    Route::get('/articledepense', function () {
        session(['nomtable' => 'articledepense']);
        return redirect()->route('element');
    });

    Route::get('/moyenpaiement', function () {
        //   session(['config' => 'elements']);
        session(['nomtable' => 'moyenpaiement']);
        return redirect()->route('element');
    });

    Route::get('/typelivraison', function () {
        session(['nomtable' => 'typelivraison']);
        return redirect()->route('element');
    });

    Route::get('/consignataire', function () {
        session(['nomtable' => 'consignataire']);
        return redirect()->route('element');
    });

    //-------------------Elements----------------
    Route::get('/element', 'App\Http\Controllers\ElementController@index')->name('element');
    Route::get('/ajout-element', 'App\Http\Controllers\ElementController@form');
    Route::get('/modifier-element/{id}', 'App\Http\Controllers\ElementController@formModif');
    Route::post('/enregistrer-element', 'App\Http\Controllers\ElementController@enregistrer');
    Route::get('/supprimer-element/{id}', 'App\Http\Controllers\ElementController@supprimer');

    //-------------------Moyentransferts----------------
    Route::get('/moyentransferts', 'App\Http\Controllers\MoyentransfertsController@index')->name('moyentransferts');
    Route::get('/ajout-moyentransferts', 'App\Http\Controllers\MoyentransfertsController@form');
    Route::get('/modifier-moyentransferts/{id}', 'App\Http\Controllers\MoyentransfertsController@formModif');
    Route::post('/enregistrer-moyentransferts', 'App\Http\Controllers\MoyentransfertsController@enregistrer');
    Route::get('/supprimer-moyentransferts/{id}', 'App\Http\Controllers\MoyentransfertsController@supprimer');

    //-------------------Catégories----------------
    Route::get('/categorie', 'App\Http\Controllers\CategorieController@index')->name('categorie');
    Route::get('/ajout-categorie', 'App\Http\Controllers\CategorieController@form');
    Route::get('/modifier-categorie/{id}', 'App\Http\Controllers\CategorieController@formModif');
    Route::post('/enregistrer-categorie', 'App\Http\Controllers\CategorieController@enregistrer');
    Route::get('/supprimer-categorie/{id}', 'App\Http\Controllers\CategorieController@supprimer');

    //-------------------Marques----------------
    Route::get('/marque', 'App\Http\Controllers\MarqueController@index')->name('marque');
    Route::get('/ajout-marque', 'App\Http\Controllers\MarqueController@form');
    Route::get('/modifier-marque/{id}', 'App\Http\Controllers\MarqueController@formModif');
    Route::post('/enregistrer-marque', 'App\Http\Controllers\MarqueController@enregistrer');
    Route::get('/supprimer-marque/{id}', 'App\Http\Controllers\MarqueController@supprimer');

    //-------------------Articles----------------
    Route::get('/article', 'App\Http\Controllers\ArticleController@index')->name('article');
    Route::get('/ajout-article', 'App\Http\Controllers\ArticleController@form');
    Route::get('/modifier-article/{id}', 'App\Http\Controllers\ArticleController@formModif');
    Route::post('/enregistrer-article', 'App\Http\Controllers\ArticleController@enregistrer');
    Route::get('/supprimer-article/{id}', 'App\Http\Controllers\ArticleController@supprimer');

    //-------------------Adresses----------------
    Route::get('/adresse', 'App\Http\Controllers\AdresseController@index')->name('adresse');
    Route::get('/ajout-adresse', 'App\Http\Controllers\AdresseController@form');
    Route::get('/modifier-adresse/{id}', 'App\Http\Controllers\AdresseController@formModif');
    Route::post('/enregistrer-adresse', 'App\Http\Controllers\AdresseController@enregistrer');
    Route::get('/supprimer-adresse/{id}', 'App\Http\Controllers\AdresseController@supprimer');

    //-------------------Acteurs----------------
    Route::get('/acteur', 'App\Http\Controllers\ActeurController@index')->name('acteur');
    Route::get('/ajout-acteur', 'App\Http\Controllers\ActeurController@form');
    Route::get('/modifier-acteur/{id}', 'App\Http\Controllers\ActeurController@formModif');
    Route::post('/enregistrer-acteur', 'App\Http\Controllers\ActeurController@enregistrer');
    Route::post('/enregistrer-acteur-ajax', 'App\Http\Controllers\ActeurController@enregistrerAjax');
    Route::get('/supprimer-acteur/{id}', 'App\Http\Controllers\ActeurController@supprimer');

    //-------------------Caisse----------------
    Route::get('/caisse', 'App\Http\Controllers\CaisseController@index')->name('caisse');
    Route::get('/ajout-caisse', 'App\Http\Controllers\CaisseController@form');
    Route::get('/modifier-caisse/{id}', 'App\Http\Controllers\CaisseController@formModif');
    Route::post('/enregistrer-caisse', 'App\Http\Controllers\CaisseController@enregistrer');
    Route::get('/supprimer-caisse/{id}', 'App\Http\Controllers\CaisseController@supprimer');

    //-------------------approcaisse----------------
    Route::get('/approcaisse', 'App\Http\Controllers\ApprocaisseController@index')->name('approcaisse');
    Route::get('/ajout-approcaisse', 'App\Http\Controllers\ApprocaisseController@form');
    Route::get('/modifier-approcaisse/{id}', 'App\Http\Controllers\ApprocaisseController@formModif');
    Route::post('/enregistrer-approcaisse', 'App\Http\Controllers\ApprocaisseController@enregistrer');
    Route::get('/supprimer-approcaisse/{id}', 'App\Http\Controllers\ApprocaisseController@supprimer');

    //-------------------depense----------------
    Route::get('/depense', 'App\Http\Controllers\DepenseController@index')->name('depense');
    Route::get('/ajout-depense', 'App\Http\Controllers\DepenseController@form');
    Route::get('/modifier-depense/{id}', 'App\Http\Controllers\DepenseController@formModif');
    Route::post('/enregistrer-depense', 'App\Http\Controllers\DepenseController@enregistrer');
    Route::get('/supprimer-depense/{id}', 'App\Http\Controllers\DepenseController@supprimer');

    //-------------------conteneur----------------
    Route::get('/conteneur', 'App\Http\Controllers\ConteneurController@index')->name('conteneur');
    Route::get('/conteneur/expeditions-disponibles', 'App\Http\Controllers\ConteneurController@expeditionsDisponibles')->name('conteneur.expeditions-disponibles');
    Route::get('/conteneur/{id}/expeditions', 'App\Http\Controllers\ConteneurController@expeditions')->name('conteneur.expeditions');
    Route::get('/ajout-conteneur', 'App\Http\Controllers\ConteneurController@form');
    Route::get('/modifier-conteneur/{id}', 'App\Http\Controllers\ConteneurController@formModif');
    Route::post('/enregistrer-conteneur', 'App\Http\Controllers\ConteneurController@enregistrer');
    Route::get('/supprimer-conteneur/{id}', 'App\Http\Controllers\ConteneurController@supprimer');

    //-------------------tauxtransfert----------------
    Route::get('/tauxtransfert', 'App\Http\Controllers\TauxtransfertController@index')->name('tauxtransfert');
    Route::get('/ajout-tauxtransfert', 'App\Http\Controllers\TauxtransfertController@form');
    Route::get('/modifier-tauxtransfert/{id}', 'App\Http\Controllers\TauxtransfertController@formModif');
    Route::post('/enregistrer-tauxtransfert', 'App\Http\Controllers\TauxtransfertController@enregistrer');
    Route::get('/supprimer-tauxtransfert/{id}', 'App\Http\Controllers\TauxtransfertController@supprimer');

    //-------------------tarifexpedition----------------
    Route::get('/tarifexpedition', 'App\Http\Controllers\TarifexpeditionController@index')->name('tarifexpedition');
    Route::get('/ajout-tarifexpedition', 'App\Http\Controllers\TarifexpeditionController@form');
    Route::get('/modifier-tarifexpedition/{id}', 'App\Http\Controllers\TarifexpeditionController@formModif');
    Route::post('/enregistrer-tarifexpedition', 'App\Http\Controllers\TarifexpeditionController@enregistrer');
    Route::get('/supprimer-tarifexpedition/{id}', 'App\Http\Controllers\TarifexpeditionController@supprimer');

    //-------------------transfert----------------
    Route::get('/transfert', 'App\Http\Controllers\TransfertController@index')->name('transfert');
    Route::get('/retrait-transfert', 'App\Http\Controllers\TransfertController@retraits')->name('transfert.retraits');
    Route::get('/ajout-transfert', 'App\Http\Controllers\TransfertController@form');
    Route::get('/modifier-transfert/{id}', 'App\Http\Controllers\TransfertController@formModif');
    Route::post('/enregistrer-transfert', 'App\Http\Controllers\TransfertController@enregistrer');
    Route::post('/enregistrer-retraitransfert', 'App\Http\Controllers\TransfertController@validerRetrait')->name('valideretraitransfert');
    Route::get('/supprimer-transfert/{id}', 'App\Http\Controllers\TransfertController@supprimer');
    Route::get('/api/retraitransfert/{id}', 'App\Http\Controllers\TransfertController@apiRetraitransfert');

    
    // Route::get('/transfert/{id}/recu-depot-transfert', function ($id) {
    //     session(['casrecu' => 'recudepot']);
    //      session(['idrecu' => $id]);
    // //    dd(session('idrecu'));
    //     return redirect()->route('transfert.recu');
    // });
    
    // Route::get('/transfert/{id}/recu-retrait-transfert', function () {
    //     session(['casrecu' => 'recuretrait']);
    //     return redirect()->route('transfert.recu');
    // });


    Route::get('/transfert/{id}/recu-depot-transfert', 'App\\Http\\Controllers\\impressionController@recuTransfertDepot')->name('transfert.recu');
    Route::get('/transfert/{id}/recu-retrait-transfert', 'App\\Http\\Controllers\\impressionController@recuTransfertRetrait')->name('transfert.recu');
    Route::get('/transfert/{id}/recu', 'App\\Http\\Controllers\\impressionController@recuTransfert')->name('transfert.recu');
    // Route::get('/transfert/{id}/recu', 'App\\Http\\Controllers\\TransfertController@recu')->name('transfert.recu');


    //-------------------expeditions----------------
    Route::get('/expedition', 'App\Http\Controllers\ExpeditionController@index')->name('expedition');
    Route::get('/expedition/{id}/historique', 'App\Http\Controllers\HistoexpeditionController@historique')->name('expedition.historique');
    Route::get('/expedition/{id}/recu-retrait', 'App\Http\Controllers\HistoexpeditionController@recuRetrait')->name('expedition.recu-retrait');
    Route::get('/expedition/{id}/paiements', 'App\Http\Controllers\PaymentcoliController@historique')->name('paymentcolis.historique');
    Route::post('/enregistrer-paymentcolis', 'App\Http\Controllers\PaymentcoliController@enregistrer')->name('paymentcolis.enregistrer');
    Route::get('/retrait-colis', 'App\Http\Controllers\ExpeditionController@retraits')->name('expedition.retraits');
    Route::get('/ajout-expedition', 'App\Http\Controllers\ExpeditionController@form');
    Route::get('/modifier-expedition/{id}', 'App\Http\Controllers\ExpeditionController@formModif');
    Route::post('/enregistrer-expedition', 'App\Http\Controllers\ExpeditionController@enregistrer');
    Route::get('/supprimer-expedition/{id}', 'App\Http\Controllers\ExpeditionController@supprimer');
    Route::post('/enregistrer-histoexpedition', 'App\Http\Controllers\HistoexpeditionController@enregistrer');
    Route::post('/enregistrer-histoconteneur', 'App\Http\Controllers\HistoconteneurController@enregistrer');


    // ===========-------------Users----------===========
    Route::get('/utilisateur', [App\Http\Controllers\UserController::class, 'index'])->name('utilisateur');
    Route::get('/ajout-utilisateur', [App\Http\Controllers\UserController::class, 'form']);
    Route::post('/enregistrer-utilisateur', [App\Http\Controllers\UserController::class, 'enregistrer']);
    Route::get('/modifier-utilisateur/{id}', [App\Http\Controllers\UserController::class, 'formModif']);
    Route::get('/supprimer-utilisateur/{id}', [App\Http\Controllers\UserController::class, 'supprimer']);

    // ===========-------------Roles & Permissions----------===========
    Route::get('/role', [App\Http\Controllers\RoleController::class, 'index'])->name('role');
    Route::get('/ajout-role', [App\Http\Controllers\RoleController::class, 'form']);
    Route::post('/enregistrer-role', [App\Http\Controllers\RoleController::class, 'enregistrer']);
    Route::get('/modifier-role/{id}', [App\Http\Controllers\RoleController::class, 'formModif']);
    Route::get('/supprimer-role/{id}', [App\Http\Controllers\RoleController::class, 'supprimer']);

    Route::get('/permission', [App\Http\Controllers\PermissionController::class, 'index'])->name('permission');
    Route::get('/ajout-permission', [App\Http\Controllers\PermissionController::class, 'form']);
    Route::post('/enregistrer-permission', [App\Http\Controllers\PermissionController::class, 'enregistrer']);
    Route::get('/modifier-permission/{id}', [App\Http\Controllers\PermissionController::class, 'formModif']);
    Route::get('/supprimer-permission/{id}', [App\Http\Controllers\PermissionController::class, 'supprimer']);

    Route::get('/rolepermission', [App\Http\Controllers\RolepermissionController::class, 'index'])->name('rolepermission');
    Route::get('/valider-droits-acces', [App\Http\Controllers\RolepermissionController::class, 'validerDroitsAcces'])->name('valider-droits-access');


    // ===========-------------Password Management----------===========
    Route::post('/changer-mot-de-passe', [App\Http\Controllers\AuthController::class, 'changerMotDePasse'])->name('changer-mot-de-passe');
    Route::post('/reinitialiser-mot-de-passe', [App\Http\Controllers\UserController::class, 'reinitialiserMotDePasse'])->name('reinitialiser-mot-de-passe');
});





//===========================Le Frontend================================================================

Route::get('/', [App\Http\Controllers\PageController::class, 'home'])->name('home'); //ok

// Route::get('/login', [App\Http\Controllers\PageController::class, 'login'])->name('login');
Route::get('/register', [App\Http\Controllers\PageController::class, 'register'])->name('register');
Route::get('/contact', [App\Http\Controllers\PageController::class, 'contact'])->name('contact');
Route::get('/brands', [App\Http\Controllers\PageController::class, 'brands'])->name('brands'); //2
Route::get('/search', [App\Http\Controllers\PageController::class, 'search'])->name('search'); //3
Route::get('/sitemap', [App\Http\Controllers\PageController::class, 'sitemap'])->name('sitemap'); //5

// Ecommerce (statique)
Route::get('/category/{slug?}', [App\Http\Controllers\PageController::class, 'category'])->name('category'); //ok attente de pagination et de bouton addcart
Route::get('/product/{slug?}', [App\Http\Controllers\PageController::class, 'product'])->name('product'); //ok
Route::get('/cart', [App\Http\Controllers\PageController::class, 'cart'])->name('cart'); //Ok
Route::get('/checkout', [App\Http\Controllers\PageController::class, 'checkout'])->name('checkout'); //4
Route::post('/checkout', [App\Http\Controllers\PageController::class, 'placeOrder'])->name('checkout.place-order');
Route::get('/commande/confirmation/{reference}', [App\Http\Controllers\PageController::class, 'orderConfirmation'])->name('order.confirmation');
Route::get('/suivi-colis', [App\Http\Controllers\PageController::class, 'suivicoli'])->name('suivicoli');
Route::get('/api/suivi-colis', [App\Http\Controllers\PageController::class, 'rechercherSuiviColis'])->name('suivi-colis.recherche');

Route::get('/add-cart', [App\Http\Controllers\PageController::class, 'addCart'])->name('addCart'); //ok
Route::get('/minus-cart', [App\Http\Controllers\PageController::class, 'minusCart'])->name('minusCart'); // Ok
Route::get('/update-session-panier', [App\Http\Controllers\PageController::class, 'updateSessionCart'])->name('updateSessionCart'); // Ok