<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\permission;  
use App\Models\rolepermission;


class PermissionController extends Controller
{
    
    public function index(Request $request)
    {  // dd($request);
        try {

            $rekete = " SELECT p.*, p1.libelle libparent FROM permissions p LEFT JOIN permissions p1 ON p.idparent = p1.id ";
            if($request->recherche != null){
              $rekete .= " WHERE p.libelle LIKE '%" . $request->recherche . "%'";
            }   
            $rekete .=" ORDER BY p.libelle ";
            $donnees = collect(DB::select($rekete));
            // $donnees = $donnees->map(function($value) {
            //     $value->libelle = $value->libelle . ' (' . $value->libparent . ')';
            //     return $value;
            // });  
            $titre = "Liste des Permissions/Fonctionnalités";
            $nouv = "ajout-permission";
            $entete = 'Libellé|Description|Parent';
            $cols = 'libelle|description|libparent';
            $btn = 'modifier|supprimer';

            session(['config' => 'permission']);
            return view('back.liste', compact(
                'donnees', 'titre', 'nouv', 'entete', 'cols', 'btn'
            ));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('permission')->with('error', 'Une erreur est survenue lors du chargement de la page.');
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
        session(['config' => 'permission']);     
         $titre = ($id != null) ? "Modification d'une fonctionnalité" : "Ajout d'une fonctionnalité";
        $valider = "enregistrer-permission"; 
        $rekete = " SELECT * FROM permissions WHERE id NOT IN('".$id."')";
        $parents = collect(DB::select($rekete)); 

        $lenregistrement = ($id != null) ? permission::find($id) : null;
         return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'titre',
            'parents',
            'valider'
        ));
    }


    public function enregistrer(Request $request)
    { //dd($request);
        try {
            
            $id = $request->input('idenreg');
            if ($id == 0 || $id == null) {
                $doub = permission::where('libelle', $request->libelle)->get();
                $e = new permission();
            } else {
                $doub = permission::where('libelle', $request->libelle)
                ->where('id', '!=', $id)
                ->get();
                $e = permission::findOrFail($id);
            }
            //dd($doub);
            if (count($doub) == 0) {
                // libelle	maternel	primaire	secondaire	universitaire	autres
              
                $e->libelle = $request->libelle;
                $e->idparent = $request->idparent;
                $e->description = $request->description;
                
                $e->save();//die();
                return redirect()->route('permission');
            } else {
                $info = "La fonctionnalité que vous tentez d'enregistrer existe déjà";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('permission')->with('error', value: "Une erreur est survenue lors de l'enregistrement de la classe-type." . $e);
        }
    }

    public function supprimer($id)
    {
        try {
            //Controle de possibilité de suppression
            $v = rolepermission::where('idpermission', $id)->get();
            if (count($v) == 0) {
                $e = permission::findOrFail($id);
                //dd($e);
                $e->delete();

                return redirect()->route('permission');
            } else {
                $info = "La fonctionalité que vous tentez de supprimer est liée à au moins un enregistrement \n Vous ne pouvez donc pas le supprimer";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('permission')->with('error', 'Une erreur est survenue lors de la suppression de l\'enregistrement.');
        }
    }
}
