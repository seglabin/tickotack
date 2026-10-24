<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\role;
use App\Models\caisse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $rekete = "";
            $rekete = "SELECT 
                users.*, 
                roles.name as role_name, 
                IF(users.actif = 1, 'Oui', 'Non') as actif_text
            FROM users
            LEFT JOIN roles ON users.idrole = roles.id
            ORDER BY users.name";   

$donnees = collect(DB::select($rekete));
        // $rekete = str_replace("\n","", $rekete);
        // $rekete = str_replace("    ","", $rekete);
        // $rekete = str_replace("  ","", $rekete);    


            // $donnees = User::leftJoin('roles', 'users.idrole', '=', 'roles.id')
            //     ->leftJoin('etablissements', 'users.idetablissement', '=', 'etablissements.id')
            //     ->select(
            //         'users.*', 
            //         'roles.name as role_name', 
            //         'etablissements.libelle as etablissement_nom',
            //         \Illuminate\Support\Facades\DB::raw('IF(users.actif = 1, "Oui", "Non") as actif_text')
            //     )
            //     ->orderBy('users.name')
            //     ->get();

            $titre = "Liste des Utilisateurs";
            $nouv = "ajout-utilisateur";
            $entete = 'Nom|Email|Rôle|Actif';
            $cols = 'name|email|role_name|actif_text';
            $btn = 'modifier|supprimer|reinitialiser';

            session(['config' => 'utilisateur']);
            return view('back.liste', compact('donnees', 'titre', 'nouv', 'entete', 'cols', 'btn'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('home')->with('error', 'Une erreur est survenue lors du chargement de la page: ' . $e->getMessage());
        }
    }

    public function reinitialiserMotDePasse(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'iduser' => 'required|exists:users,id',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            $user = User::findOrFail($request->iduser);
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->route('utilisateur')->with('success', 'Le mot de passe de l\'utilisateur a été réinitialisé avec succès.');
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('utilisateur')->with('error', 'Une erreur est survenue lors de la réinitialisation : ' . $e->getMessage());
        }
    }

    public function formModif(Request $request, $id)
    {
        $request['idenreg'] = $id;
        return $this->form($request);
    }

    public function form(Request $request)
    {
        try {
            $request->validate([
                'idenreg' => 'nullable|exists:users,id',
            ]);
            $idenreg = $request['idenreg'];
        session(['config' => 'utilisateur']);

        $lenregistrement = ($idenreg != null) ? User::find($idenreg) : null;
        $roles = role::orderBy('name')->get();
        $caisses = caisse::orderBy('libelle')->get();
       
        return view('back.formulaires.formUser', compact(
            'lenregistrement',
            'idenreg',
            'roles',
            'caisses'
        ));

        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('utilisateur')->with('error', 'Une erreur est survenue lors du chargement du formulaire: ' . $e->getMessage());
        }

    }

    public function enregistrer(Request $request)
    {
        $id = $request->input('idenreg');
        $nouvelUtilisateur = !$id;
        $donnees = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'idrole' => ['required', 'exists:roles,id'],
            'idcaisse' => ['nullable', 'integer', 'exists:caisses,id'],
            'password' => $nouvelUtilisateur ? ['required', 'string', 'min:6'] : ['nullable', 'string', 'min:6'],
        ]);

        try {
            if ($nouvelUtilisateur) {
                $e = new User();
                $e->password = Hash::make($donnees['password']);
            } else {
                $e = User::findOrFail($id);
                if (!empty($donnees['password'])) {
                    $e->password = Hash::make($donnees['password']);
                }
            }

            $e->name = $donnees['name'];
            $e->username = $donnees['username'];
            $e->email = $donnees['email'];
            $e->idrole = $donnees['idrole'];
            $e->idcaisse = $donnees['idcaisse'] ?? null;
            $e->actif = $request->has('actif') ? 1 : 0;
            
            $e->save();

            return redirect()->route('utilisateur');

        } catch (\Exception $e) {
            return redirect()->route('utilisateur')->with('error', 'Une erreur est survenue lors de l\'enregistrement de l\'utilisateur. ' . $e->getMessage());
        }
    }

    public function supprimer($id)
    {
        try {
            $e = User::findOrFail($id);
            $e->delete();
            return redirect()->route('utilisateur');
        } catch (\Exception $e) {
            return redirect()->route('utilisateur')->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }
}
