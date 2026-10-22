<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\element;


class ElementController extends Controller
{
     public function index(Request $request)
    {   //dd($request);
        // dd(session('nomtable'));
        //typecolis, moyentransport, etat, sourceappro, typenvoi,articledepense
        $titre = "";
        try {
            $nomtable = session('nomtable');
            if ($nomtable == 'typecolis') $titre = "Liste des types de colis";
            if ($nomtable == 'moyentransport') $titre = "Liste des moyens de transport";
            if ($nomtable == 'etat') $titre = "Liste des états";
            if ($nomtable == 'sourceappro') $titre = "Liste des sources d'approvisionnement";
            if ($nomtable == 'typenvoi') $titre = "Liste des types d'envoi";
            if ($nomtable == 'articledepense') $titre = "Liste des articles de dépense";
            if ($nomtable == 'moyenpaiement') $titre = "Liste des moyens de paiement";
            if ($nomtable == 'typelivraison') $titre = "Liste des types de livraison";
            if ($nomtable == 'consignataire') $titre = "Liste des consignataires";

            $rekete = "SELECT * FROM elements WHERE nomtable = '" . $nomtable . "'  ORDER BY libelle";
            $donnees = collect(DB::select($rekete));
            $nouv = '/ajout-element';
            $entete = 'Libellé';
            $cols = 'libelle';
            if(in_array($nomtable, [ 'etat'])) {
                $entete = 'Cas|Libellé|Valeur';
                $cols = 'lecas|libelle|valeur';
            }
            $btn = 'modifier|supprimer';
            // if ($nomtable == 'typeoperation') $btn .= '|associer';

            session(['config' => 'element']);
            session(['module' => $nomtable]);
            return view('back.liste', compact(
                'donnees',
                'nomtable',
                'titre',
                'nouv',
                'entete',
                'cols',
                'btn'
            ));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('element')->with('error', 'Une erreur est survenue lors du chargement de la page.');
        }
    }


    public function formModif(Request $request, $id)
    {
        $request['idenreg'] = $id;
        return $this->form($request);
    }

    public function form(Request $request)
    {
        //typecolis, moyentransport, etat, sourceappro, typenvoi,articledepense 
        $id = $request['idenreg'];
        $nomtable = session('nomtable'); //dd($nomtable);
        if ($nomtable == 'typecolis') $titre = ($id != null) ? "Modification d'un type de colis" : "Ajout d'un type de colis";
        if ($nomtable == 'moyentransport') $titre = ($id != null) ? "Modification d'un moyen de transport" : "Ajout d'un moyen de transport";
        if ($nomtable == 'etat') $titre = ($id != null) ? "Modification d'un état" : "Ajout d'un état";
        if ($nomtable == 'sourceappro') $titre = ($id != null) ? "Modification d'une source d'approvisionnement" : "Ajout d'une source d'approvisionnement";
        if ($nomtable == 'typenvoi') $titre = ($id != null) ? "Modification d'un type d'envoi" : "Ajout d'un type d'envoi";
        if ($nomtable == 'articledepense') $titre = ($id != null) ? "Modification d'un article de dépense" : "Ajout d'un article de dépense";
        if ($nomtable == 'moyenpaiement') $titre = ($id != null) ? "Modification d'un moyen de paiement" : "Ajout d'un moyen de paiement";
        if ($nomtable == 'typelivraison') $titre = ($id != null) ? "Modification d'un type de livraison" : "Ajout d'un type de livraison";
        if ($nomtable == 'consignataire') $titre = ($id != null) ? "Modification d'un consignataire" : "Ajout d'un consignataire";
        session(['config' => 'element']);
        $valider = "enregistrer-element";

        $lenregistrement = ($id != null) ? element::find($id) : null;
        return view('back.formulaire', compact(
            'lenregistrement',
            'id',
            'nomtable',
            'titre',
            'valider'
        ));
    }


    public function enregistrer(Request $request)
    {
        //  dd($request);
        try {

            $id = $request->input('idenreg');
            if ($id == 0 || $id == null) {
                $doub = element::where('libelle', $request->libelle)
                    ->where('nomtable', $request->nomtable)->get();
                $e = new element();
            } else {
                $doub = element::where('libelle', $request->libelle)
                    ->where('nomtable', $request->nomtable)
                    ->where('id', '!=', $id)->get();
                $e = element::findOrFail($id);
            }
            //dd(count($doub));
            if (count($doub) == 0) {

                $e->libelle = $request->libelle;
                $e->nomtable = $request->nomtable;

                if ($request->lecas != null) $e->lecas = $request->lecas;
                if ($request->valeur != null) $e->valeur = $request->valeur;
                $e->save(); //die();
                session(['nomtable' => $request->nomtable]);
                return redirect()->route('element');
            } else {
                $info = "L'élément que vous tentez d'enregistrer existe déjà";
                $titre = "DOUBLON";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->route('element')->with('error', 'Une erreur est survenue lors de l\'enregistrement de l\'élément.' . $e);
        }
    }

    public function supprimer($id)
    {
        $nomtable = session('nomtable');
        //dd($nomtable);
        try {
            //Controle de possibilité de suppression
            $v = array();
            switch ($nomtable) {
                case 'circonscolaire':
                    // $v = subvention::where('idcirconscolaire', $id)->get();
                    break;
            }
            if (count($v) == 0) {
                $e = element::findOrFail($id);
                //dd($e);
                $e->delete();

                return redirect()->route('element');
            } else {
                $info = "L'élément que vous tentez de supprimer est lié à au moins un autre enregistrement \n Vous ne pouvez donc pas la supprimer";
                $titre = "CONTRAINTE D'INTEGRITE";
                return view('alertDoublon', compact('info', 'titre'));
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('element')->with('error', 'Une erreur est survenue lors de la suppression de l\'enregistrement.');
        }
    }
}
