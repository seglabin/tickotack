<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $rekslider = " SELECT * FROM articles WHERE carousel = true ORDER BY libelle LIMIT 4 ";
        $sliders = collect(DB::select($rekslider));
// dd($sliders);
        $rekcategtop = " SELECT c.*, COALESCE( (SELECT COUNT('a.id') FROM articles a WHERE idcategorie = c.id GROUP BY idcategorie),0) nbprod ";
        $rekcategtop .= " FROM categories c WHERE top = true AND visible = true ORDER BY libelle ";
        $categtop = collect(DB::select($rekcategtop));
        $categvisible = collect(DB::select(" SELECT * FROM categories WHERE visible = true ORDER BY libelle "));
        $user = Auth::check() ? Auth::user() : null;

        $config = session('config') ?? 'site';

        //dd($user);
        if ($user != null && $config != 'site') {
            return view('back.accueilA');
        } else
            // Home reste “template-first”. On n’injecte des donnees que si tu veux rendre certaines sections dynamiques plus tard.
            return view('pages.home', compact('sliders', 'categtop', 'categvisible'));
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


    public function category(string $slug = null)
    {
        $catalog = $this->catalog();
        // $currentCategory = null;
        // foreach ($catalog['categories'] as $c) {
        //     if ($slug && $c['slug'] === $slug) { $currentCategory = $c; break; }
        // }

        $currentCategory = collect(DB::select(" SELECT * FROM categories WHERE id = '" . $slug . "' "))->first();
        // dd($currentCategory);
        return view('pages.category', [
            'slug' => $slug,
            'categories' => $catalog['categories'],
            'brands' => $catalog['brands'],
            'currentCategory' => $currentCategory,
            'products' => $this->productsByCategory($slug),
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
        $panier = session('panier') != null ? session('panier')  : array();

        return view('pages.checkout', compact('panier'));
    }
}
