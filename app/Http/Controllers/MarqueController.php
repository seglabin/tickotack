<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\marque;

class MarqueController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
           // dd($request);
        try {
            
            $titre = "Liste des marques";
            $nouv = "/ajout-marque";
            $entete = "Titre|Description";
            $cols = "libelle|description";
            $btn = "modifier|supprimer";

            $rekete = "SELECT * FROM marques ORDER BY libelle";
           
            $donnees = collect(DB::select($rekete));

            session(['config' => 'marque']);
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
            return redirect()->route('marque')->with('error', 'Une erreur est survenue lors du chargement de la page.');
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
            $titre = "Ajouter une marque";
            $photo = "";
            $valider = "enregistrer-marque";
            $id = $request['idenreg'];
            session(['config' => 'marque']);
            session(['idenreg' => $id]);
            $lenregistrement = ($id != null) ? marque::find($id) : null;
            if ($lenregistrement) {
                $titre = "Modifier une marque";
                $photo = 'storage/images/Marques/' . $lenregistrement->photo;
                //  dd($photo);
            }

              $rekete = "SELECT * FROM marques WHERE id <> '".$id."' ORDER BY libelle";
           
           
            return view('back.formulaire', compact('lenregistrement', 'titre', 'photo', 'valider', 'id'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('marque')->with('error', 'Une erreur est survenue lors du chargement du formulaire.');
        }
    }



    function enregistrer(Request $request)
    {
        try {
            //  dd($request);
            $idenreg = $request->idenreg;
            $rekdoub = " SELECT * FROM marques WHERE libelle = '" . addslashes($request->libelle ). "' ";
            if ($idenreg == null) {
                $e = new marque();
            } else {
                $e = marque::find($idenreg);
                $rekdoub .= " AND id != '" . $idenreg . "' ";
            }

            $doub = DB::select($rekdoub);
            if (count($doub) == 0) {
                $photo = saveFichier($request, 'photo', 'storage/images/Marques');

                $e->libelle =$request->libelle;
                $e->description = $request->description;
                $e->visible = ($request->visible!= null)?$request->visible:false;
                if ($photo != null)
                    $e->photo = $photo;
                $e->save();
                return redirect()->route('marque');
            } else {
                 $info = "La marque que vous tentez d'enregistrer existe déjà.";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
                
            }

        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('marque');
        }

    }



    public function supprimer($id)
    {
          try {
              //Controle de possibilité de suppression
              $rek = " SELECT * FROM articles WHERE idmarque = '".$id."' ";
               $supctrl = DB::select($rek);
              
              if (count($supctrl) == 0) {           

                  $e = marque::findOrFail($id);
                  //dd($e);
                  $e->delete();                  

                  return redirect()->route('marques');
              } else {
                  $info = "La marque que vous tentez de supprimer est liée à au moins un enregistrement \n Vous ne pouvez donc pas la supprimer";
                  $titre = "CONTRAINTE D'INTEGRITE";
                  return view('alertDoublon', compact('info', 'titre'));
              }
          } catch (\Exception $e) {
              dd($e);
              return redirect()->route('marque')->with('error', 'Une erreur est survenue lors de la suppression de l\'enregistrement.');
          }
    }


}

