<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\acteur;
use App\Models\adresse;
use App\Models\User;

class ActeurController extends Controller
{
    public function index(Request $request)
    {
        try {
            $titre = "Liste des acteurs (expéditeurs / récepteurs)";

            $donnees = collect(DB::select("
                SELECT a.id, a.nom, a.tel, a.whatsap, a.carte, a.marque,
                       ad.libelle as adresse_livraison
                FROM acteurs a
                LEFT JOIN adresses ad ON ad.id = a.idadreslivr
                ORDER BY a.nom
            "));

            $nouv = '/ajout-acteur';
            $entete = 'Nom|Tél|WhatsApp|Carte|Adresse livraison';
            $cols = 'nom|tel|whatsap|carte|adresse_livraison';
            $btn = 'modifier|supprimer';

            session(['config' => 'acteur']);
            session(['module' => 'acteurs']);
            return view('back.liste', compact(
                'donnees',
                'titre',
                'nouv',
                'entete',
                'cols',
                'btn'
            ));
        } catch (\Exception $e) {
            return redirect()->route('acteur')->with('error', 'Erreur de chargement : ' . $e->getMessage());
        }
    }

    public function formModif(Request $request, $id)
    {
        $request['idenreg'] = $id;
        return $this->form($request);
    }

    public function form(Request $request)
    {
        $id = $request['idenreg'] ?? null;
        $titre = ($id != null) ? "Modification d'un acteur" : "Ajout d'un acteur";
        session(['config' => 'acteur']);
        session(['module' => 'acteurs']);
        $valider = "enregistrer-acteur";

        $lenregistrement = ($id != null) ? acteur::find($id) : null;
        $adresses = adresse::orderBy('libelle')->get();

        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'valider',
            'adresses'
        ));
    }

    public function enregistrer(Request $request)
    {
        try {
            $id = $request->input('idenreg');
            if ($id == 0 || $id == null) {
                $doub = acteur::where('nom', $request->nom)
                    ->where('tel', $request->tel)->get();
                $e = new acteur();
            } else {
                $doub = acteur::where('nom', $request->nom)
                    ->where('tel', $request->tel)
                    ->where('id', '!=', $id)->get();
                $e = acteur::findOrFail($id);
            }

            if (count($doub) == 0) {
                $e->nom        = $request->nom;
                $e->tel        = $request->tel;
                $e->whatsap    = $request->whatsap;
                $e->marque     = $request->marque;
                if ($id == 0 || $id == null) {
                    $e->login = loginActeur();
                    $e->password = 'password';
                    $e->default_password = 'password';
                }
                // $e->carte      = $request->carte;
                $carte = saveFichier($request, 'carte', 'storage/images/Acteurs/', 'CARTE_ACT_');
                if ($carte != null) $e->carte = $carte;
                $e->idadreslivr = ($request->idadreslivr && $request->idadreslivr != '0') ? $request->idadreslivr : null;
                $e->save();
                return redirect()->route('acteur');
            } else {
                $info = "L'acteur que vous tentez d'enregistrer existe déjà (même nom et téléphone).";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('acteur')->with('error', 'Erreur d\'enregistrement : ' . $e->getMessage());
        }
    }

    public function enregistrerAjax(Request $request)
    {
        try {
            if (!$request->nom || !$request->tel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le nom et le numéro de téléphone sont obligatoires.'
                ], 422);
            }

            $doub = acteur::where('nom', $request->nom)
                ->where('tel', $request->tel)
                ->first();

            if ($doub) {
                return response()->json([
                    'success' => false,
                    'message' => "Un acteur avec ce nom et ce téléphone existe déjà."
                ], 409);
            }

            $e = new acteur();
            $e->nom = $request->nom;
            $e->tel = $request->tel;
            $e->whatsap = $request->whatsap;
            $e->marque = $request->marque;
            $e->idadreslivr = ($request->idadreslivr && $request->idadreslivr != '0') ? $request->idadreslivr : null;
            //Création d'user
            $u = new User();
            $u->name = $request->nom;
            $username = loginActeur(); // Génération d'un nom d'utilisateur unique
            $u->username = $username; // Génération d'un nom d'utilisateur unique
            $u->email = $username . '@example.com'; // Génération d'un email fictif
            $u->idrole = 2; // Rôle par défaut pour les acteurs
            //$u->idcaisse = $request->idcaisse ? $request->idcaisse : null;
            $u->actif = $request->has('actif') ? 1 : 0;
            $u->password = Hash::make('password'); // Mot de passe par défaut

            if ($u->save()) {
                $e->iduser = $u->id;
            }

            // $e->login = loginActeur();
            // $e->password = 'password';
            // $e->default_password = 'password';


            // if ($request->hasFile('carte')) {
            //     $file = $request->file('carte');
            //     $filename = time() . '_' . $file->getClientOriginalName();
            //     $dest = public_path('storage/images/Acteurs');
            //     if (!\Illuminate\Support\Facades\File::exists($dest)) {
            //         \Illuminate\Support\Facades\File::makeDirectory($dest, 0755, true);
            //     }
            //     $file->move($dest, $filename);
            //     $e->carte = $filename;
            // }

            $carte = saveFichier($request, 'carte', 'storage/images/Acteurs/', 'CARTE_ACT_');
            if ($carte != null) $e->carte = $carte;

            $e->save();

            return response()->json([
                'success' => true,
                'acteur' => [
                    'id' => $e->id,
                    'nom' => $e->nom,
                    'tel' => $e->tel
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ], 500);
        }
    }

    public function supprimer($id)
    {
        try {
            $hasTransferts = DB::table('transferts')
                ->where('idexpediteur', $id)
                ->orWhere('idrecepteur', $id)
                ->exists();

            if (!$hasTransferts) {
                $e = acteur::findOrFail($id);
                $e->delete();
                return redirect()->route('acteur');
            } else {
                $info = "Cet acteur est lié à au moins un transfert.\nVous ne pouvez pas le supprimer.";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            return redirect()->route('acteur')->with('error', 'Erreur de suppression : ' . $e->getMessage());
        }
    }
}
