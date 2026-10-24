<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\role;
use App\Models\User;


class RoleController extends Controller
{

    public function index(Request $request)
    {   //dd($request);
        try {
            $rekete = " SELECT * FROM roles ";
            if ($request->recherche != null) {
                $rekete .= " WHERE name LIKE '%" . $request->recherche . "%'";
            }
            $rekete .= " ORDER BY name ";
            $donnees = collect(DB::select($rekete));

            $titre = "Liste des profils utilisateurs";
            $nouv = "ajout-role";
            $entete = 'Nom du rôle|Code';
            $cols = 'name|code';
            $btn = 'modifier|supprimer';

            session(['config' => 'role']);
            return view('back.liste', compact('donnees', 'titre', 'nouv', 'entete', 'cols', 'btn'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('role')->with('error', 'Une erreur est survenue lors du chargement de la page.');
        }
    }


    public function formModif(Request $request, $id)
    {
        $request['idenreg'] = $id;
        return $this->form($request);
    }

    public function form(Request $request)
    {
        $id = $request['idenreg'];
        session(['config' => 'role']);
        $titre = ($id != null) ? "Modification d'un profil utilisateur" : "Ajout d'un profil utilisateur";
        $valider = "enregistrer-role";

        $lenregistrement = ($id != null) ? Role::find($id) : null;
        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'valider'
        ));
    }


    public function enregistrer(Request $request)
    { //dd($request);
        try {

            $id = $request->input('idenreg');
            if ($id  == 0 || $id == null) {
                $doub = role::where('name', $request->name);
                $e = new Role();
            } else {
                $doub = role::where('name', $request->name)
                    ->where('id', '!=', $id)
                    ->get();
                $e = role::findOrFail($id);
            }
            //dd($doub);
            if ($doub) {
                //`designation`, `contact`, `email`, `adresse`
                $e->name = $request->name;
                $e->code = $request->code;

                $e->save(); //die();
                return redirect()->route('role');
            } else {
                $info = "Le profil que vous tentez d'enregistrer existe déjà";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            return redirect()->route('role')->with('error', 'Une erreur est survenue lors de l\'enregistrement du profil.' . $e);
        }
    }

    public function supprimer($id)
    {
        try {
            //Controle de possibilité de suppression
            $v = user::where('idrole', $id)->get();
            if (count($v) == 0) {
                $e = role::findOrFail($id);
                //dd($e);
                $e->delete();

                return redirect()->route('role');
            } else {
                $info = "Le profile que vous tentez de supprimer est lié à au moins un utilisateur \n Vous ne pouvez donc pas le supprimer";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('client')->with('error', 'Une erreur est survenue lors de la suppression de l\'enregistrement.');
        }
    }
}
