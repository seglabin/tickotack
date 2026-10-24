<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\categorie;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // dd($request);
        try {

            $titre = "Liste des catégories";
            $nouv = "/ajout-categorie";
            $entete = "Titre|Description";
            $cols = "libelle|description";
            $btn = "modifier|supprimer";

            $rekete = "SELECT * FROM categories ORDER BY libelle";

            $donnees = collect(DB::select($rekete));

            session(['config' => 'categorie']);
            return view('back.liste', compact(
                'donnees',
                'titre',
                'nouv',
                'entete',
                'cols',
                'btn',

            ));
        } catch (\Exception $e) {
            return redirect()->route('categorie')->with('error', 'Une erreur est survenue lors du chargement de la page.');
        }
    }


    public function formModif(Request $request, $id)
    {  //dd($id);
        $request['idenreg'] = $id;
        return $this->form($request);
    }


    function form(Request $request)
    {
        try {
            // dd($e);
            $titre = "Ajouter une catégorie";
            $photo = "";
            $valider = "enregistrer-categorie";
            $id = $request['idenreg'];
            session(['config' => 'categorie']);
            session(['idenreg' => $id]);
            $lenregistrement = ($id != null) ? Categorie::find($id) : null;
            if ($lenregistrement) {
                $titre = "Modifier une catégorie";
                $photo = 'storage/images/Categories/' . $lenregistrement->photo;
                //  dd($photo);
            }

            $rekete = "SELECT * FROM categories WHERE id <> '" . $id . "' ORDER BY libelle";
            $parents = collect(DB::select($rekete));

            // dd($rekete);

            return view('back.formulaire', compact('lenregistrement', 'titre', 'photo', 'valider', 'id', 'parents'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('categorie')->with('error', 'Une erreur est survenue lors du chargement du formulaire.');
        }
    }



    function enregistrer(Request $request)
    {
        try {
            //  dd($request);
            $idenreg = $request->idenreg;
            $rekdoub = " SELECT * FROM categories WHERE libelle = '" . addslashes($request->libelle) . "'  ";
            if ($idenreg == null) {
                $e = new Categorie();
            } else {
                $e = Categorie::find($idenreg);
                $rekdoub .= " AND id != '" . $idenreg . "' ";
            }

            $doub = DB::select($rekdoub);
            if (count($doub) == 0) {
                $photo = saveFichier($request, 'photo', 'storage/images/Categories');

                $e->libelle = $request->libelle;
                $e->description = $request->description;
                $e->idparent = $request->idparent;
                $e->top = ($request->top != null) ? $request->top : false;
                $e->visible = ($request->visible != null) ? $request->visible : false;
                if ($photo != null)
                    $e->photo = $photo;
                $e->save();
                return redirect()->route('categorie');
            } else {
                $info = "La catégorie que vous tentez d'enregistrer existe déjà.";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));                
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('categorie');
        }
    }



    public function supprimer($id)
    {
        try {
            //Controle de possibilité de suppression
            $rek = " ( SELECT id FROM categories WHERE idparent = '" . $id . "' )";
            $rek .= " UNION ";
            $rek .= " (SELECT id FROM articles WHERE idcategorie = '" . $id . "' ) ";
            $supctrl = DB::select($rek);
            // dd(count($supctrl));

            if (count($supctrl) == 0) {

                $e = Categorie::findOrFail($id);
                //dd($e);
                $e->delete();

                return redirect()->route('categories');
            } else {
                $info = "La catégorie que vous tentez de supprimer est liée à au moins un enregistrement \n Vous ne pouvez donc pas la supprimer";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('categories')->with('error', 'Une erreur est survenue lors de la suppression de l\'enregistrement.');
        }
    }
}
