<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\article;
use App\Models\categorie;
use App\Models\marque;


class ArticleController extends Controller
{
      public function index()
    {
        // dd($request);


        try {

            $titre = "Liste des articles";
            $nouv = "/ajout-article";
            $entete = "Libellé|Description|PU|Catégorie|Marque";
            $cols = "libelle|description|prix|libcategorie|libmarque";
            $btn = "modifier|supprimer";

            $rekete = "SELECT a.* ";
            $rekete .= ", c.libelle AS libcategorie ";
            $rekete .= ", COALESCE((SELECT m.libelle FROM marques m WHERE m.id = a.idmarque ),'') libmarque  ";
            $rekete .= " FROM articles a, categories c ";
            $rekete .= " WHERE a.idcategorie = c.id ";
            $rekete .= " ORDER BY libelle";

            // dd($rekete);
            $donnees = collect(DB::select($rekete));

            session(['config' => 'article']);
            return view('back.liste', compact(
                'donnees',
                'titre',
                'nouv',
                'entete',
                'cols',
                'btn',
            ));

        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('article')->with('error', 'Une erreur est survenue lors du chargement de la page.');
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
            $titre = "Ajouter un article";
            $photo = "";
            $valider = "enregistrer-article";
            $id = $request['idenreg'];
            session(['config' => 'article']);
            session(['idenreg' => $id]);
            $lenregistrement = ($id != null) ? article::find($id) : null;
            $titre = ($lenregistrement != null) ? "Modifier un article" : "Ajouter un article";

            $photo1 = ($lenregistrement != null) ? 'storage/images/Articles/' . $lenregistrement->photo1 : null;
            $photo2 = ($lenregistrement != null) ? 'storage/images/Articles/' . $lenregistrement->photo2 : null;
            $photo3 = ($lenregistrement != null) ? 'storage/images/Articles/' . $lenregistrement->photo3 : null;
            $photo4 = ($lenregistrement != null) ? 'storage/images/Articles/' . $lenregistrement->photo4 : null;
            $photo5 = ($lenregistrement != null) ? 'storage/images/Articles/' . $lenregistrement->photo5 : null;
            //  dd($photo);


            $rekparent = "SELECT * FROM articles WHERE id <> '" . $id . "' AND lot = 1 ORDER BY libelle";
            $parents = collect(DB::select($rekparent));
            $categories = Categorie::orderBy('libelle')->get();
            $marques = Marque::orderBy('libelle')->get();

            return view('back.formulaire', compact(
                'lenregistrement',
                'titre',
                'valider',
                'id',
                'parents',
                'photo1',
                'photo2',
                'photo3',
                'photo4',
                'photo5',
                'categories',
                'marques'
            ));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('article')->with('error', 'Une erreur est survenue lors du chargement du formulaire.');
        }
    }



    function enregistrer(Request $request)
    {
        try {
            //   dd($request);
            $idenreg = $request->idenreg;
            $rekdoub = " SELECT * FROM articles WHERE libelle = '" . addslashes($request->libelle) . "' ";
            $rekdoub .= " AND idcategorie = '" . $request->idcategorie. "' ";
            if ($idenreg == null) {
                $e = new article();
            } else {
                $e = article::find($idenreg);
                $rekdoub .= " AND id != '" . $idenreg . "' ";
            }

            $doub = DB::select($rekdoub);
            if (count($doub) == 0) {
                //`libelle`, `reference`, `description`, `photo1`, `photo2`, `photo3`, `photo4`, `photo5`, `lot`, `carousel`, `idparent`, `idcategorie`, `idmarque`
                $photo1 = saveFichier($request, 'photo1', 'storage/images/Articles','ART_');
                $photo2 = saveFichier($request, 'photo2', 'storage/images/Articles','ART_');
                $photo3 = saveFichier($request, 'photo3', 'storage/images/Articles','ART_');
                $photo4 = saveFichier($request, 'photo4', 'storage/images/Articles','ART_');
                $photo5 = saveFichier($request, 'photo5', 'storage/images/Articles','ART_');

                $e->libelle = $request->libelle;
                $e->reference = $request->reference;
                $e->description = $request->description;
                $e->lot = ($request->lot!= null)?$request->lot:0;
                $e->carousel = ($request->carousel!= null)?$request->carousel:0;
                $e->idparent = $request->idparent;
                $e->prix = sansespace($request->prix);
                $e->idcategorie = $request->idcategorie;
                $e->idmarque = $request->idmarque;
                if ($photo1 != null) $e->photo1 = $photo1;
                if ($photo2 != null) $e->photo2 = $photo2;
                if ($photo3 != null) $e->photo3 = $photo3;
                if ($photo4 != null) $e->photo4 = $photo4;
                if ($photo5 != null) $e->photo5 = $photo5;
                $e->save();
                return redirect()->route('article');
            } else {
                 $info = "L'article que vous tentez d'enregistrer existe déjà.";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));                
            }

        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('article');
        }

    }



    public function supprimer($id)
    {
        try {
            //Controle de possibilité de suppression
            // $rek = " SELECT * FROM articles WHERE idmarque = '" . $id . "' ";
            // $supctrl = DB::select($rek);
$supctrl = array();
            if (count($supctrl) == 0) {

                $e = Article::findOrFail($id);
                //dd($e);
                $e->delete();

                return redirect()->route('article');
            } else {
                $info = "L' article que vous tentez de supprimer est liée à au moins un enregistrement \n Vous ne pouvez donc pas le supprimer";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('article')->with('error', 'Une erreur est survenue lors de la suppression de l\'enregistrement.');
        }
    }


}
