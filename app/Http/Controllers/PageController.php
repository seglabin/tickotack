<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PageController extends Controller
{

    private function produitParCriterekkk($critere = "")
    {
        $rekete = " SELECT a.*, c.libelle libcategorie, COALESCE((SELECT libelle FROM marques WHERE id = a.idmarque),'') libmarque  ";
        $rekete .= " FROM articles a, categories c ";
        $rekete .= " WHERE c.id = a.idcategorie ";
        $rekete .= ($critere != '') ? " AND " . $critere : "";
        return  collect(DB::select($rekete));
    }
    /**
     * Donnees statiques (sans base de donnees) pour simuler un catalogue.
     * Objectif: permettre de maqueter toutes les vues/UX avant d'attaquer le back-office.
     */
    private function catalog(): array
    {
        // $categories = [
        //     ['name' => 'Papeterie', 'slug' => 'papeterie', 'count' => 42, 'image' => 'image/cache/catalog/menu-icon/daily%20need%20002-190x140-190x140.png'],
        //     ['name' => 'Informatique', 'slug' => 'informatique', 'count' => 28, 'image' => 'image/cache/catalog/menu-icon/CLEANING%20003-190x140-190x140.png'],
        //     ['name' => 'Impression', 'slug' => 'impression', 'count' => 19, 'image' => 'image/cache/catalog/menu-icon/BEVRAGES%20004-190x140-190x140.png'],
        //     ['name' => 'Classement', 'slug' => 'classement', 'count' => 31, 'image' => 'image/cache/catalog/menu-icon/BABY%20CARE-190x140-190x140.png'],
        // ];

        $rekcateg = "  SELECT c.*, id AS slug ";
        $rekcateg .= " , COALESCE( (SELECT COUNT('a.id') FROM articles a WHERE idcategorie = c.id GROUP BY idcategorie),0) nbprod ";
        $rekcateg .= " FROM categories c WHERE visible = true ORDER BY libelle ";
        $categories = collect(DB::select($rekcateg));


        // $brands = [
        //     ['name' => 'HP', 'slug' => 'hp'],
        //     ['name' => 'Canon', 'slug' => 'canon'],
        //     ['name' => 'Epson', 'slug' => 'epson'],
        //     ['name' => 'Bic', 'slug' => 'bic'],
        //     ['name' => 'Oxford', 'slug' => 'oxford'],
        // ];

        $brands = collect(DB::select(" SELECT m.*, id AS slug FROM marques m WHERE visible = true ORDER BY libelle "));

        // $products = [
        //     ['id' => 101, 'name' => 'Ramette Papier A4 80g', 'slug' => 'ramette-papier-a4-80g', 'category' => 'papeterie', 'brand' => 'oxford', 'price' => 3.90, 'old_price' => 4.50, 'image' => 'image/cache/catalog/product/01-300x300.png', 'images' => ['image/cache/catalog/product/01-700x700.png','image/cache/catalog/product/02-700x700.png','image/cache/catalog/product/03-700x700.png'], 'rating' => 4],
        //     ['id' => 102, 'name' => 'Stylo bille Bleu', 'slug' => 'stylo-bille-bleu', 'category' => 'papeterie', 'brand' => 'bic', 'price' => 0.60, 'old_price' => null, 'image' => 'image/cache/catalog/product/02-300x300.png', 'images' => ['image/cache/catalog/product/02-700x700.png','image/cache/catalog/product/04-700x700.png'], 'rating' => 5],
        //     ['id' => 103, 'name' => 'Classeur A4 2 anneaux', 'slug' => 'classeur-a4-2-anneaux', 'category' => 'classement', 'brand' => 'oxford', 'price' => 2.40, 'old_price' => 3.00, 'image' => 'image/cache/catalog/product/03-300x300.png', 'images' => ['image/cache/catalog/product/03-700x700.png','image/cache/catalog/product/01-700x700.png'], 'rating' => 4],
        //     ['id' => 104, 'name' => 'Cartouche Encre Noire (compatible)', 'slug' => 'cartouche-encre-noire-compatible', 'category' => 'impression', 'brand' => 'canon', 'price' => 9.90, 'old_price' => 12.00, 'image' => 'image/cache/catalog/product/04-300x300.png', 'images' => ['image/cache/catalog/product/04-700x700.png','image/cache/catalog/product/02-700x700.png'], 'rating' => 3],
        //     ['id' => 105, 'name' => 'Souris USB Optique', 'slug' => 'souris-usb-optique', 'category' => 'informatique', 'brand' => 'hp', 'price' => 5.90, 'old_price' => null, 'image' => 'image/cache/catalog/product/05-300x300.png', 'images' => ['image/cache/catalog/product/05-700x700.png','image/cache/catalog/product/06-700x700.png'], 'rating' => 4],
        //     ['id' => 106, 'name' => 'Clavier AZERTY USB', 'slug' => 'clavier-azerty-usb', 'category' => 'informatique', 'brand' => 'hp', 'price' => 12.90, 'old_price' => 15.90, 'image' => 'image/cache/catalog/product/06-300x300.png', 'images' => ['image/cache/catalog/product/06-700x700.png','image/cache/catalog/product/05-700x700.png'], 'rating' => 4],
        //     ['id' => 107, 'name' => 'Agrafeuse métal', 'slug' => 'agrafeuse-metal', 'category' => 'papeterie', 'brand' => 'bic', 'price' => 6.50, 'old_price' => 7.90, 'image' => 'image/cache/catalog/product/07-300x300.png', 'images' => ['image/cache/catalog/product/07-700x700.png','image/cache/catalog/product/08-700x700.png'], 'rating' => 5],
        //     ['id' => 108, 'name' => 'Toner Laser Noir (compatible)', 'slug' => 'toner-laser-noir-compatible', 'category' => 'impression', 'brand' => 'epson', 'price' => 24.90, 'old_price' => 29.90, 'image' => 'image/cache/catalog/product/08-300x300.png', 'images' => ['image/cache/catalog/product/08-700x700.png','image/cache/catalog/product/04-700x700.png'], 'rating' => 4],
        // ];
        $products = articleParCritere();


        return compact('categories', 'brands', 'products');
    }

    private function findProductBySlug(string $slug)
    {
        return articleParCritere(" a.id = '" . $slug . "' ");
    }

    private function productsByCategory(?string $categorySlug)
    {
        // $catalog = $this->catalog();
        // if (!$categorySlug) return $catalog['products'];
        // return array_values(array_filter($catalog['products'], fn($p) => $p['category'] === $categorySlug));
        //dd($categorySlug);
        $critere = $categorySlug != null ? " idcategorie = '" . $categorySlug . "' " : '';
        return articleParCritere($critere);
    }

    public function home()
    {
        $user = Auth::check() ? Auth::user() : null;
        $config = session('config') ?? 'site';

        if ($user != null && $config != 'site') {
            $user->loadMissing('role');
            $roleName = $user->role
                ? Str::lower(Str::ascii($user->role->code . ' ' . $user->role->name))
                : '';
            $isAdministrator = Str::contains($roleName, 'admin');

            if ($isAdministrator) {
                $today = now()->toDateString();
                $dashboardStats = [
                    'users' => DB::table('users')->where('actif', true)->count(),
                    'expeditions' => DB::table('expeditions')->whereDate('datenreg', $today)->count(),
                    'transfers' => DB::table('transferts')->whereDate('datetransfert', $today)->count(),
                    'cashboxes' => DB::table('caisses')->count(),
                    'pendingShipments' => DB::table('expeditions as e')
                        ->leftJoinSub(
                            DB::table('paymentcolis')
                                ->select('idcolis')
                                ->selectRaw('SUM(montant) as total')
                                ->groupBy('idcolis'),
                            'payments',
                            'payments.idcolis',
                            '=',
                            'e.id'
                        )
                        ->whereRaw('e.montant > COALESCE(payments.total, 0) + COALESCE(e.montantpaye, 0)')
                        ->count(),
                    'pendingTransfers' => DB::table('transferts')->whereNull('datepayement')->count(),
                ];
                $recentExpeditions = DB::table('expeditions as e')
                    ->leftJoin('adresses as destination', 'destination.id', '=', 'e.iddestination')
                    ->leftJoin('elements as etat', 'etat.id', '=', 'e.idetat')
                    ->select('e.code', 'e.datenreg', 'e.nbrcolis', 'destination.libelle as destination', 'etat.libelle as etat')
                    ->orderByDesc('e.datenreg')
                    ->limit(5)
                    ->get();
                $recentTransfers = DB::table('transferts as t')
                    ->leftJoin('acteurs as sender', 'sender.id', '=', 't.idexpediteur')
                    ->leftJoin('acteurs as receiver', 'receiver.id', '=', 't.idrecepteur')
                    ->leftJoin('adresses as destination', 'destination.id', '=', 't.iddestination')
                    ->leftJoin('elements as etat', 'etat.id', '=', 't.idetat')
                    ->select(
                        't.code',
                        't.datetransfert',
                        't.montretrait',
                        't.montpaye',
                        't.datepayement',
                        'sender.nom as expediteur',
                        'receiver.nom as recepteur',
                        'destination.libelle as destination',
                        'etat.libelle as etat'
                    )
                    ->orderByDesc('t.datetransfert')
                    ->limit(5)
                    ->get();

                $paymentsByExpedition = DB::table('paymentcolis')
                    ->select('idcolis')
                    ->selectRaw('SUM(montant) as total')
                    ->groupBy('idcolis');
                $expeditionPaymentsByCashbox = DB::table('expeditions as e')
                    ->leftJoinSub($paymentsByExpedition, 'payments', 'payments.idcolis', '=', 'e.id')
                    ->whereNotNull('e.idcaisse')
                    ->groupBy('e.idcaisse')
                    ->select('e.idcaisse')
                    ->selectRaw('SUM(COALESCE(payments.total, 0) + COALESCE(e.montantpaye, 0)) as total');
                $transferPaymentsByCashbox = DB::table('operationfinancieres')
                    ->where('libsrce', 'transferts')
                    ->where('sens', 'Sortie')
                    ->groupBy('idcaisse')
                    ->select('idcaisse')
                    ->selectRaw('SUM(montant) as total');
                $cashboxPaymentComparison = DB::table('caisses as c')
                    ->leftJoinSub($expeditionPaymentsByCashbox, 'expedition_payments', 'expedition_payments.idcaisse', '=', 'c.id')
                    ->leftJoinSub($transferPaymentsByCashbox, 'transfer_payments', 'transfer_payments.idcaisse', '=', 'c.id')
                    ->select(
                        'c.id',
                        'c.libelle',
                        'c.devise',
                        DB::raw('COALESCE(expedition_payments.total, 0) as expeditions_payees'),
                        DB::raw('COALESCE(transfer_payments.total, 0) as transferts_payes')
                    )
                    ->orderBy('c.libelle')
                    ->get();
                $recentCashAdvances = DB::table('approcaisses as a')
                    ->leftJoin('caisses as c', 'c.id', '=', 'a.idcaisse')
                    ->leftJoin('elements as source', 'source.id', '=', 'a.idsource')
                    ->select('a.dateappro', 'a.montant', 'a.observation', 'c.libelle as caisse', 'source.libelle as source')
                    ->orderByDesc('a.dateappro')
                    ->limit(5)
                    ->get();

                return view('back.accueilA', compact(
                    'dashboardStats',
                    'recentExpeditions',
                    'recentTransfers',
                    'cashboxPaymentComparison',
                    'recentCashAdvances'
                ));
            }

            $userCashbox = $user->idcaisse
                ? DB::table('caisses as c')
                    ->leftJoin('adresses as a', 'a.id', '=', 'c.idadresse')
                    ->where('c.id', $user->idcaisse)
                    ->first(['c.libelle as caisse', 'c.idadresse', 'a.libelle as adresse'])
                : null;
            $cashboxAddressId = $userCashbox->idadresse ?? null;
            $recentExpeditions = $cashboxAddressId
                ? DB::table('expeditions as e')
                    ->leftJoin('acteurs as sender', 'sender.id', '=', 'e.idexpediteur')
                    ->leftJoin('adresses as destination', 'destination.id', '=', 'e.iddestination')
                    ->leftJoin('elements as etat', 'etat.id', '=', 'e.idetat')
                    ->where('e.iddestination', $cashboxAddressId)
                    ->select('e.code', 'e.datenreg', 'sender.nom as expediteur', 'destination.libelle as destination', 'etat.libelle as etat')
                    ->orderByDesc('e.datenreg')
                    ->limit(5)
                    ->get()
                : collect();
            $recentTransfers = $cashboxAddressId
                ? DB::table('transferts as t')
                    ->leftJoin('acteurs as sender', 'sender.id', '=', 't.idexpediteur')
                    ->leftJoin('acteurs as receiver', 'receiver.id', '=', 't.idrecepteur')
                    ->leftJoin('adresses as destination', 'destination.id', '=', 't.iddestination')
                    ->where('t.iddestination', $cashboxAddressId)
                    ->select('t.code', 't.datetransfert', 't.datepayement', 'sender.nom as expediteur', 'receiver.nom as recepteur', 'destination.libelle as destination')
                    ->orderByDesc('t.datetransfert')
                    ->limit(5)
                    ->get()
                : collect();

            $availablePermissions = session('menusUser', []);
            $dashboardActions = [
                ['permission' => 3, 'title' => 'Dépenses', 'description' => 'Consulter et enregistrer les dépenses.', 'url' => url('depense'), 'icon' => 'fa-receipt', 'color' => 'orange'],
                ['permission' => 4, 'title' => 'Expéditions', 'description' => 'Gérer les colis et leurs retraits.', 'url' => url('expedition'), 'icon' => 'fa-box', 'color' => 'purple'],
                ['permission' => 5, 'title' => 'Conteneurs', 'description' => 'Suivre les expéditions regroupées.', 'url' => url('conteneur'), 'icon' => 'fa-shipping-fast', 'color' => 'green'],
                ['permission' => 6, 'title' => 'Transferts', 'description' => 'Enregistrer les dépôts et retraits.', 'url' => url('transfert'), 'icon' => 'fa-exchange-alt', 'color' => 'teal'],
                ['permission' => 19, 'title' => 'Adresses', 'description' => 'Gérer les villes et destinations.', 'url' => url('adresse'), 'icon' => 'fa-map-marker-alt', 'color' => 'orange'],
                ['permission' => 20, 'title' => 'Acteurs', 'description' => 'Gérer les expéditeurs et destinataires.', 'url' => url('acteur'), 'icon' => 'fa-users', 'color' => 'blue'],
                ['permission' => 30, 'title' => 'Utilisateurs', 'description' => 'Consulter les comptes utilisateurs.', 'url' => url('utilisateur'), 'icon' => 'fa-user-cog', 'color' => 'purple'],
                ['permission' => 31, 'title' => 'Profils et rôles', 'description' => 'Gérer les profils de la plateforme.', 'url' => url('role'), 'icon' => 'fa-user-shield', 'color' => 'teal'],
                ['permission' => 33, 'title' => 'Droits d’accès', 'description' => 'Configurer les autorisations des profils.', 'url' => url('rolepermission'), 'icon' => 'fa-lock', 'color' => 'green'],
            ];
            $dashboardActions = array_values(array_filter(
                $dashboardActions,
                fn (array $action) => is_array($availablePermissions)
                    && in_array($action['permission'], $availablePermissions)
            ));
            $roleLabel = $user->role->name ?? 'Collaborateur';

            return view('back.accueilB', compact(
                'dashboardActions',
                'roleLabel',
                'userCashbox',
                'recentExpeditions',
                'recentTransfers'
            ));
        }

        $sliders = DB::table('articles')
            ->where('carousel', true)
            ->orderBy('libelle')
            ->limit(4)
            ->get();

        $categtop = collect(DB::select("
            SELECT c.*,
                   (SELECT COUNT(*) FROM articles a WHERE a.idcategorie = c.id) AS nbprod
            FROM categories c
            WHERE c.top = true AND c.visible = true
            ORDER BY c.libelle
        "));
        $categvisible = DB::table('categories')
            ->where('visible', true)
            ->orderBy('libelle')
            ->get();
        $homeProducts = DB::table('articles as a')
            ->leftJoin('marques as m', 'm.id', '=', 'a.idmarque')
            ->select('a.*', DB::raw("COALESCE(m.libelle, '') AS libmarque"))
            ->orderByDesc('a.id')
            ->limit(24)
            ->get();
        $homeCategories = $categvisible
            ->filter(fn ($category) => $homeProducts->contains('idcategorie', $category->id))
            ->values();
        $homeBrands = DB::table('marques')
            ->where('visible', true)
            ->orderBy('libelle')
            ->limit(12)
            ->get();
        return view('pages.home', compact(
            'sliders',
            'categtop',
            'categvisible',
            'homeProducts',
            'homeCategories',
            'homeBrands'
        ));
    }

    public function suivicoli()
    {
        return redirect()->route('home', ['suivi' => 1]);
    }

    public function rechercherSuiviColis(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255'],
        ]);

        $expedition = DB::table('expeditions as e')
            ->leftJoin('acteurs as exp', 'exp.id', '=', 'e.idexpediteur')
            ->leftJoin('acteurs as rec', 'rec.id', '=', 'e.idrecepteur')
            ->leftJoin('adresses as dep', 'dep.id', '=', 'e.iddepart')
            ->leftJoin('adresses as dest', 'dest.id', '=', 'e.iddestination')
            ->leftJoin('elements as etat', 'etat.id', '=', 'e.idetat')
            ->leftJoin('conteneurs as co', 'co.id', '=', 'e.idconteneur')
            ->where('e.code', trim($validated['code']))
            ->select([
                'e.code',
                'e.datenreg',
                'e.nbrcolis',
                'e.poids',
                'exp.nom as expediteur',
                'rec.nom as recepteur',
                'dep.libelle as depart',
                'dest.libelle as destination',
                'etat.libelle as etat',
                'co.numconteneur as conteneur',
                'co.lienconteneur',
            ])
            ->first();

        if (!$expedition) {
            return response()->json([
                'message' => 'Aucune expédition ne correspond à ce numéro de colis.',
            ], 404);
        }

        $historique = DB::table('histoexpeditions as h')
            ->leftJoin('elements as etat', 'etat.id', '=', 'h.idetat')
            ->where('h.idexpedition', function ($query) use ($validated) {
                $query->select('id')
                    ->from('expeditions')
                    ->where('code', trim($validated['code']))
                    ->limit(1);
            })
            ->orderByDesc('h.datetat')
            ->select(['h.datetat', 'h.reference', 'etat.libelle as etat'])
            ->get();

        return response()->json([
            'expedition' => $expedition,
            'historique' => $historique,
        ]);
    }

    // public function login() { return view('pages.login'); }
    public function register()
    {
        return view('pages.register');
    }
    public function contact()
    {
        return view('pages.contact');
    }
    public function brands()
    {
        return view('pages.brands');
    }
    public function sitemap()
    {
        return view('pages.sitemap');
    }
    public function search()
    {
        return view('pages.search');
    }


    public function addCart(Request $request)
    {
        $id = $request->idproduitAjout;
        $qte = $request->qteAjout;
        $prod = articleParCritere(" a.id = '" . $id . "' ")->first();

        if ($prod != null) {
            $img = "storage/images/Articles/" . $prod->photo1;
            $prix = $prod->prix;
            $libelle = $prod->libelle;
            $libcategorie = $prod->libcategorie;
            $libmarque = $prod->libmarque;

            $panier = session('panier') != null ? session('panier')  : array();
            $ind = rechercherElement($panier, 'idproduit', $id);

            if ($ind !== false) {
                $panier[$ind]['qte'] += $qte;
                $panier[$ind]['montant'] = ($panier[$ind]['qte'] * $prix);
            } else {  //idproduit   libelle photo   pu  qte montant

                $nb = count($panier);
                $ln = array();
                $ln['idproduit'] = $id;
                $ln['libelle'] = $libelle;
                $ln['libmarque'] = $libmarque;
                $ln['libcategorie'] = $libcategorie;
                $ln['photo'] = $img;
                $ln['pu'] = $prix;
                $ln['qte'] = $qte;
                $ln['montant'] = ($qte * $prix);
                array_push($panier, $ln);
            }
        }
        session(['panier' => $panier]);

        return back();
    }

    public function minusCart(Request $request)
    {
        $id = $request->idproduitAjout;

        $panier = session('panier') != null ? session('panier')  : array();
        $ind = rechercherElement($panier, 'idproduit', $id);
        if ($ind !== false) {
            array_splice($panier, $ind, 1);
            // unset($panier[$ind]);
            // $panier = array_values($panier);

        }

        session(['panier' => $panier]);

        return back();
    }


    public function category(Request $request, ?string $slug = null)
    {
        $filters = $request->validate([
            'brand' => [
                'nullable',
                'integer',
                Rule::exists('marques', 'id')->where(fn ($query) => $query->where('visible', true)),
            ],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'sort' => ['nullable', Rule::in(['popular', 'name', 'priceAsc', 'priceDesc'])],
        ]);

        $currentCategory = $slug === null
            ? null
            : DB::table('categories')->where('id', $slug)->first();

        $categories = DB::table('categories as c')
            ->where('c.visible', true)
            ->select('c.*')
            ->selectSub(
                DB::table('articles as category_articles')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('category_articles.idcategorie', 'c.id'),
                'nbprod'
            )
            ->orderBy('c.libelle')
            ->get();

        $brandsQuery = DB::table('marques as m')
            ->join('articles as brand_articles', 'brand_articles.idmarque', '=', 'm.id')
            ->where('m.visible', true)
            ->select('m.id', 'm.libelle')
            ->distinct()
            ->orderBy('m.libelle');
        if ($slug !== null) {
            $brandsQuery->where('brand_articles.idcategorie', $slug);
        }
        $brands = $brandsQuery->get();

        $productsQuery = DB::table('articles as a')
            ->leftJoin('marques as m', 'm.id', '=', 'a.idmarque')
            ->select('a.*', DB::raw("COALESCE(m.libelle, '') AS libmarque"));
        if ($slug !== null) {
            $productsQuery->where('a.idcategorie', $slug);
        }
        if (!empty($filters['brand'])) {
            $productsQuery->where('a.idmarque', $filters['brand']);
        }
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $productsQuery->where('a.prix', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $productsQuery->where('a.prix', '<=', $filters['max_price']);
        }

        switch ($filters['sort'] ?? 'popular') {
            case 'name':
                $productsQuery->orderBy('a.libelle')->orderBy('a.id');
                break;
            case 'priceAsc':
                $productsQuery->orderBy('a.prix')->orderBy('a.id');
                break;
            case 'priceDesc':
                $productsQuery->orderByDesc('a.prix')->orderByDesc('a.id');
                break;
            default:
                $productsQuery->orderByDesc('a.created_at')->orderByDesc('a.id');
                break;
        }

        $products = $productsQuery->paginate(12)->withQueryString();

        return view('pages.category', [
            'slug' => $slug,
            'categories' => $categories,
            'brands' => $brands,
            'currentCategory' => $currentCategory,
            'products' => $products,
            'filters' => [
                'brand' => (string) ($filters['brand'] ?? ''),
                'min_price' => $filters['min_price'] ?? '',
                'max_price' => $filters['max_price'] ?? '',
                'sort' => $filters['sort'] ?? 'popular',
            ],
        ]);
    }

    public function product($slug = null)
    {
        // $product = [
        //     'slug' => $slug ?: 'apple-cinema-30',
        //     'name' => 'APPLE CINEMA 30"',
        //     'brand' => 'Apple',
        //     'sku' => 'Product 15',
        //     'reward_points' => 100,
        //     'reward_price' => 400,
        //     'availability' => 'En stock',
        //     'price' => 108.80,
        //     'price_tiers' => [100 => 107.60, 200 => 94.40, 300 => 81.20],
        //     'category_slug' => 'papeterie',
        //     'category_name' => 'Papeterie',
        //     'images' => [
        //         'image/products/product1.png',
        //         'image/products/product2.png',
        //         'image/products/product3.png',
        //         'image/products/product4.png',
        //     ],
        //     'options' => ['Option A', 'Option B'],
        //     'description_html' => '<p>Texte description (maquette).</p>',
        //     'specs' => [
        //         ['Nom' => 'Marque', 'Valeur' => 'Apple'],
        //         ['Nom' => 'Modèle', 'Valeur' => 'Product 15'],
        //     ],
        // ];

        // $relatedProducts = [
        //     ['slug'=>'htc-touch-hd','name'=>'HTC Touch HD','price'=>104.00,'old_price'=>0,'image'=>'image/products/product2.png'],
        //     ['slug'=>'iphone','name'=>'iPhone','price'=>122.00,'old_price'=>0,'image'=>'image/products/product3.png'],
        //     ['slug'=>'imac','name'=>'iMac','price'=>122.00,'old_price'=>0,'image'=>'image/products/product4.png'],
        // ];

        $product = articleParCritere(" a.id = '" . $slug . "' ")->first();

        $relatedProducts = ($product != null) ? articleParCritere(" a.id != '" . $slug . "' AND  ( idcategorie = '" . $product->idcategorie . "' OR idmarque = '" . $product->idmarque . "')") : null;

        return view('pages.product', compact('product', 'relatedProducts'));
    }

    public function cart()
    {
        $panier = session('panier') != null ? session('panier')  : array();

        return view('pages.cart', compact('panier'));
    }

    public function updateSessionCart(Request $request)
    {
        $id = $request->idproduitAjout;
        $qte = $request->qteAjout;

        $panier = session('panier') != null ? session('panier')  : array();
        $ind = rechercherElement($panier, 'idproduit', $id);
        if ($ind !== false) {
            $panier[$ind]['qte'] = $qte;
            $panier[$ind]['montant'] = ($panier[$ind]['qte'] * $panier[$ind]['pu']);
        }
        session(['panier' => $panier]);

        return back();
    }

    public function checkout()
    {
        $panier = session('panier', []);
        if (!is_array($panier) || $panier === []) {
            return redirect()->route('cart')->with('error', 'Votre panier est vide.');
        }

        try {
            $cart = $this->normalizeCheckoutCart($panier);
        } catch (ValidationException $exception) {
            return redirect()->route('cart')->with('error', $exception->errors()['panier'][0]);
        }

        session(['panier' => $cart['items']]);
        $panier = $cart['items'];
        $subtotal = $cart['subtotal'];
        $typesLivraison = DB::table('elements')
            ->where('nomtable', 'typelivraison')
            ->orderBy('libelle')
            ->get();

        return view('pages.checkout', compact('panier', 'subtotal', 'typesLivraison'));
    }

    public function placeOrder(Request $request)
    {
        $panier = session('panier', []);
        if (!is_array($panier) || $panier === []) {
            return redirect()->route('cart')->with('error', 'Votre panier est vide.');
        }

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
            'idtypelivraison' => [
                'required',
                'integer',
                Rule::exists('elements', 'id')->where(fn ($query) => $query->where('nomtable', 'typelivraison')),
            ],
            'adresse' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:255'],
            'pays' => ['nullable', 'string', 'max:100'],
            'observation' => ['nullable', 'string', 'max:2000'],
        ]);

        $typeLivraison = DB::table('elements')->where('id', $validated['idtypelivraison'])->first();
        $retraitClient = Str::lower(trim($typeLivraison->libelle)) === Str::lower('Moi même');
        if (!$retraitClient) {
            $validated = array_merge($validated, $request->validate([
                'adresse' => ['required', 'string', 'max:255'],
                'ville' => ['required', 'string', 'max:255'],
                'pays' => ['required', 'string', 'max:100'],
            ]));
        }

        try {
            $cart = $this->normalizeCheckoutCart($panier);
        } catch (ValidationException $exception) {
            return redirect()->route('cart')->with('error', $exception->errors()['panier'][0]);
        }

        $fraisLivraison = $retraitClient ? 0 : 2.50;
        $taxe = round($cart['subtotal'] * 0.18, 2);
        $total = round($cart['subtotal'] + $fraisLivraison + $taxe, 2);
        $reference = (string) Str::uuid();

        DB::transaction(function () use ($validated, $cart, $retraitClient, $fraisLivraison, $taxe, $total, $reference) {
            $client = DB::table('clients')->where('email', $validated['email'])->first();
            if ($client === null) {
                $clientId = DB::table('clients')->insertGetId([
                    'nom' => $validated['nom'],
                    'telephone' => $validated['telephone'],
                    'email' => $validated['email'],
                    'adresgeo' => $retraitClient ? null : $validated['adresse'],
                    'compte' => 'guest-' . Str::uuid(),
                    'password' => Hash::make(Str::random(40)),
                    'desactif' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $clientId = $client->id;
            }

            $orderId = DB::table('commandes')->insertGetId([
                'datecmd' => now(),
                'observation' => $validated['observation'] ?? null,
                'idclient' => $clientId,
                'idtypelivraison' => $validated['idtypelivraison'],
                'reference' => $reference,
                'nomclient' => $validated['nom'],
                'emailclient' => $validated['email'],
                'telephoneclient' => $validated['telephone'],
                'adresse_livraison' => $retraitClient ? null : ($validated['adresse'] ?? null),
                'ville_livraison' => $retraitClient ? null : ($validated['ville'] ?? null),
                'pays_livraison' => $retraitClient ? null : ($validated['pays'] ?? null),
                'methode_paiement' => 'paiement_a_la_livraison',
                'sous_total' => $cart['subtotal'],
                'frais_livraison' => $fraisLivraison,
                'taxe' => $taxe,
                'total' => $total,
                'statut' => 'En attente',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($cart['items'] as $item) {
                DB::table('paniers')->insert([
                    'qte' => $item['qte'],
                    'pu' => $item['pu'],
                    'idcmd' => $orderId,
                    'idarticle' => $item['idproduit'],
                    'libelle' => $item['libelle'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $etatInitial = DB::table('elements')
                ->where('nomtable', 'etat')
                ->where('libelle', 'Enregistrement')
                ->value('id');
            if ($etatInitial !== null) {
                DB::table('historiquecmds')->insert([
                    'dateheure' => now(),
                    'observation' => 'Commande enregistrée sur la boutique en ligne.',
                    'idcmd' => $orderId,
                    'idetat' => $etatInitial,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        session()->forget('panier');

        return redirect()->route('order.confirmation', ['reference' => $reference]);
    }

    public function orderConfirmation(string $reference)
    {
        $commande = DB::table('commandes as c')
            ->join('elements as el', 'el.id', '=', 'c.idtypelivraison')
            ->where('c.reference', $reference)
            ->select('c.*', 'el.libelle as typelivraison')
            ->first();

        abort_if($commande === null, 404);

        $articles = DB::table('paniers')
            ->where('idcmd', $commande->id)
            ->orderBy('id')
            ->get();

        return view('pages.order-confirmation', compact('commande', 'articles'));
    }

    private function normalizeCheckoutCart(array $panier): array
    {
        if ($panier === []) {
            throw ValidationException::withMessages(['panier' => 'Votre panier est vide.']);
        }

        $ids = [];
        foreach ($panier as $item) {
            if (!is_array($item) || !isset($item['idproduit']) || !ctype_digit((string) $item['idproduit'])) {
                throw ValidationException::withMessages(['panier' => 'Un produit de votre panier n’est plus valide.']);
            }

            $quantite = filter_var($item['qte'] ?? null, FILTER_VALIDATE_INT);
            if ($quantite === false || $quantite < 1) {
                throw ValidationException::withMessages(['panier' => 'La quantité d’un produit de votre panier n’est pas valide.']);
            }

            $ids[] = (int) $item['idproduit'];
        }

        $products = DB::table('articles as a')
            ->leftJoin('categories as c', 'c.id', '=', 'a.idcategorie')
            ->leftJoin('marques as m', 'm.id', '=', 'a.idmarque')
            ->whereIn('a.id', $ids)
            ->select('a.id', 'a.libelle', 'a.prix', 'a.photo1', 'c.libelle as libcategorie', 'm.libelle as libmarque')
            ->get()
            ->keyBy('id');

        $items = [];
        $subtotal = 0;
        foreach ($panier as $item) {
            $product = $products->get((int) $item['idproduit']);
            if ($product === null) {
                throw ValidationException::withMessages(['panier' => 'Un produit de votre panier n’est plus disponible.']);
            }

            $quantite = (int) $item['qte'];
            $prix = round((float) $product->prix, 2);
            $montant = round($prix * $quantite, 2);
            $items[] = [
                'idproduit' => $product->id,
                'libelle' => $product->libelle,
                'photo' => 'storage/images/Articles/' . $product->photo1,
                'libcategorie' => $product->libcategorie,
                'libmarque' => $product->libmarque,
                'pu' => $prix,
                'qte' => $quantite,
                'montant' => $montant,
            ];
            $subtotal += $montant;
        }

        return ['items' => $items, 'subtotal' => round($subtotal, 2)];
    }
}
