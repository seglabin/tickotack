<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf; // Import de la façade


class ImpressionController extends Controller
{
    public function imprimer(Request $request)
    {
        // dd($request); donneeimprim  coltitre  coldata
        // dd(session('config'));
        try {
            $idabonnement = (count($_GET) > 0 && isset($_GET['idabonnement'])) ? $_GET['idabonnement'] : (session('idabonnement') != null ? session('idabonnement') : session('idabonnementEncours'));
            $idanneescolaire = (count($_GET) > 0 && isset($_GET['idanneescolaire'])) ? $_GET['idanneescolaire'] : (session('idanneescolaire') != null ? session('idanneescolaire') : null);
            $letitre = (count($_GET) > 0 && isset($_GET['letitre'])) ? $_GET['letitre'] : 'Titre document'; //idanneescolaire
            $idclassannesco = (count($_GET) > 0 && isset($_GET['idclassannesco'])) ? $_GET['idclassannesco'] : (session('idclassannescosEncours') != null ? session('idclassannescosEncours') : null);
            $idinscription = (count($_GET) > 0 && isset($_GET['idinscription'])) ? $_GET['idinscription'] : null;
            $idsession = (count($_GET) > 0 && isset($_GET['idsession'])) ? $_GET['idsession'] : null;
            $config = session('config') != null ? session('config') : '';
            $labonne = $idabonnement != null ? collect(DB::select(" SELECT * FROM abonnements WHERE id = '" . $idabonnement . "' "))->first() : null;
            $lannesco = $idanneescolaire != null ? collect(DB::select(" SELECT a.*, CONCAT(andebut,' - ',(andebut + 1)) libannee FROM anneescolaires a WHERE id = '" . $idanneescolaire . "' "))->first() : null;
            $laclannesco = $idclassannesco != null ? collect(DB::select(" SELECT ca.*, CONCAT(libelle, ' ', COALESCE(groupe, '')) libclas, CONCAT(sigle, ' / ', COALESCE(groupe, '')) libclasabrev FROM classannescos ca , classetypes c WHERE ca.idclasse = c.id AND  ca.id = '" . $idclassannesco . "' "))->first() : null;
            $lapprenant = $idinscription != null ? collect(DB::select(" SELECT ins.*, CONCAT(nom, ' ', prenoms) libapprenant FROM inscriptions ins, apprenants a , personnes p WHERE a.idpersonne = p.id AND ins.idapprenant = a.id AND  ins.id = '" . $idinscription . "' "))->first() : null;
            $lasession = $idsession != null ? collect(DB::select(" SELECT * FROM sessionacademiques WHERE id = '" . $idsession . "' "))->first() : null;
            $donneeimprim = session('donneeimprim') != null ? session('donneeimprim') : array();
            $coltitre = session('coltitre') != null ? session('coltitre') : '';
            $coldata = session('coldata') != null ? session('coldata') : '';
            $hautdoc = array();
            $ln = array();
            $frmat = 'A4';
            $donneestats = null;
            //  dd($config); 
            $pag = in_array($config, array('moyenne-periode-apprenant', 'details-composition', 'liste-educmaster', 'note-educmaster')) ? 'landscape' : 'portrait';
            $module = session('module') != null ? session('module') : null;
        //    dd($laclannesco);
           if($module == 'recapCompositionAbonne') $pag = 'landscape';
            $fichier = $letitre . '.pdf';
            // dd($donneeimprim); recapCompositionAbonne
            // dd($config);
            // switch ($config) {
            //     case 'moyenne-generale-periode':
            //     case 'moyenne-periode-apprenant':
            //     case 'anneescolaire':
            //     case 'role':
            //     case 'personne':
            //     case 'session-academique':
            //     case 'coefficient':
            //     case 'matiere':
            //     case 'utilisateur':
            //     case 'classetype':
            //     case 'apprenant':
            //     case 'classannesco':
            //     case 'abonnement':
            //     case 'paiement':
            //     case 'paramfrais':
            //     case 'evaluation':
            //     case 'composition':
            //     case 'moyenne-periode':
            //     case 'permission':
            //     case 'element':
            if ($lannesco != null) {
                array_push($ln, 'Année scolaire ');
                array_push($ln, $lannesco->libannee);
            }
            if ($laclannesco != null) {
                array_push($ln, 'Classe Année scolaire ');
                array_push($ln, $laclannesco->libclas);
            }
            if ($lasession != null) {
                array_push($ln, 'Session académique ');
                array_push($ln, $lasession->libelle);
            }
            if (count($ln) > 0)
                array_push($hautdoc, $ln);
            $ln = array();
            if ($lapprenant != null) {
                array_push($ln, 'Apprenant : ');
                array_push($ln, $lapprenant->libapprenant);
            }
            if (count($ln) > 0)
                array_push($hautdoc, $ln);
            $stats = array();

            if ($config == 'details-composition') {
                $idenreg = session('idenreg') != null ? session('idenreg') : null;
                //compositions`.`idclassannesco`    classannescos    `inscriptions`.`idapprenant`  `apprenants`.`idpersonne` `personnes`.`idsexe`   
                $moy = 12;
                $rk = " SELECT c.*  ";

                $rk .= ", COALESCE(COUNT(CASE WHEN p.idsexe = 7 THEN 1 END),0) AS nbG ";
                $rk .= ", COALESCE(COUNT(CASE WHEN p.idsexe = 8 THEN 1 END),0) AS nbF ";
                // $rk .=", COALESCE((SELECT COUNT(m.id) FROM moyennecompos m WHERE moyenne > 0 AND idcompo = c.id    ),0) AS nbcompo  ";
                $rk .= ", COALESCE((SELECT COUNT(CASE WHEN p.idsexe = 7 THEN 1 END ) FROM moyennecompos m, personnes p,inscriptions ins, apprenants a  WHERE ins.idapprenant = a.id AND a.idpersonne = p.id AND ins.id = m.idinscription AND moyenne > 0 AND idcompo = c.id    ),0) AS nbGcompo  ";
                $rk .= ", COALESCE((SELECT COUNT(CASE WHEN p.idsexe = 8 THEN 1 END ) FROM moyennecompos m, personnes p,inscriptions ins, apprenants a  WHERE ins.idapprenant = a.id AND a.idpersonne = p.id AND ins.id = m.idinscription AND moyenne > 0 AND idcompo = c.id    ),0) AS nbFcompo  ";
                $rk .= ", COALESCE((SELECT COUNT(CASE WHEN p.idsexe = 7 THEN 1 END ) FROM moyennecompos m, personnes p,inscriptions ins, apprenants a  WHERE ins.idapprenant = a.id AND a.idpersonne = p.id AND ins.id = m.idinscription AND moyenne >= " . $moy . " AND idcompo = c.id    ),0) AS nbGmoy  ";
                $rk .= ", COALESCE((SELECT COUNT(CASE WHEN p.idsexe = 8 THEN 1 END ) FROM moyennecompos m, personnes p,inscriptions ins, apprenants a  WHERE ins.idapprenant = a.id AND a.idpersonne = p.id AND ins.id = m.idinscription AND moyenne >= " . $moy . " AND idcompo = c.id    ),0) AS nbFmoy  ";
                $rk .= "";
                $rk .= " FROM compositions c, classannescos ca, inscriptions ins, apprenants a, personnes p ";
                $rk .= " WHERE ca.id =  c.idclassannesco  ";
                $rk .= " AND ins.idclassannesco = ca.id ";
                $rk .= " AND ins.idapprenant = a.id ";
                $rk .= " AND a.idpersonne = p.id ";
                $rk .= " AND c.id = '" . $idenreg . "' ";
                $rk .= " GROUP BY c.idclassannesco  ";

                $donneestats = collect(DB::select($rk))->first();

                // dd($donneestats);

            }

            //         break;

            // }
            // dd($hautdoc);
            //  dd($donneeimprim);


            // session(['config' => 'abonnement']);
            $pdf = PDF::loadView('print.liste', compact(
                'donneeimprim',
                'labonne',
                'coldata',
                'coltitre',
                'hautdoc',
                'letitre',
                'config',
                'donneestats',
                'laclannesco',

            ))->setPaper($frmat, $pag);
            $pdf->setBasePath(public_path());

            // return $pdf->stream();             ->setPaper('a4', 'landscape')
            return $pdf->stream($fichier, ['Content-Disposition' => 'inline']);

            // return view('print.listePortrait', compact(
            //     'donnees',
            //     'labonne'                
            // ));

        } catch (\Exception $e) {
            dd($e);
            return redirect()->back();
        }
    }


    public function cartes(Request $request)
    {
        //dd($request);
        // --- IGNORE ---
        try {
            $idabonnement = (count($_GET) > 0 && isset($_GET['idabonnement'])) ? $_GET['idabonnement'] : (session('idabonnement') != null ? session('idabonnement') : session('idabonnementEncours'));
            $labonne = $idabonnement != null ? collect(DB::select(" SELECT * FROM abonnements WHERE id = '" . $idabonnement . "' "))->first() : null;
            $idanneescolaire = (count($_GET) > 0 && isset($_GET['idanneescolaire'])) ? $_GET['idanneescolaire'] : null;
            $lannesco = $idanneescolaire != null ? collect(DB::select(" SELECT a.*, CONCAT(andebut,' - ',(andebut + 1)) libannee FROM anneescolaires a WHERE id = '" . $idanneescolaire . "' "))->first() : null;
            $config = session('config') != null ? session('config') : '';
            $donneeimprim = session('donneeimprim') != null ? session('donneeimprim') : array();

            $letitre = (count($_GET) > 0 && isset($_GET['letitre'])) ? $_GET['letitre'] : 'Titre document';
            $fichier = $letitre . '.pdf';

            $frmat = 'A4';
            $pag = in_array($config, array('moyenne-periode-apprenant')) ? 'landscape' : 'portrait';

            $pdf = PDF::loadView('print.cartes', compact(
                'donneeimprim',
                'labonne',
                'lannesco',


            ))->setPaper($frmat, $pag);
            $pdf->setBasePath(public_path());
            // return $pdf->stream();             ->setPaper('a4', 'landscape')
            return $pdf->stream($fichier, ['Content-Disposition' => 'inline']);
           
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back();
        }

    }



    public function bulletin(Request $request)
    {
        // dd($_GET);
        // --- IGNORE ---
        try {
            $ln = (count($_GET) > 0 && isset($_GET['ln'])) ? $_GET['ln'] : null;
            $idabonnement = (count($_GET) > 0 && isset($_GET['idabonnement'])) ? $_GET['idabonnement'] : (session('idabonnement') != null ? session('idabonnement') : session('idabonnementEncours'));
            $labonne = $idabonnement != null ? collect(DB::select(" SELECT * FROM abonnements WHERE id = '" . $idabonnement . "' "))->first() : null;
            $idanneescolaire = (count($_GET) > 0 && isset($_GET['idanneescolaire'])) ? $_GET['idanneescolaire'] : null;
            $lannesco = $idanneescolaire != null ? collect(DB::select(" SELECT a.*, CONCAT(andebut,' - ',(andebut + 1)) libannee FROM anneescolaires a WHERE id = '" . $idanneescolaire . "' "))->first() : null;
            $idclassannesco = (count($_GET) > 0 && isset($_GET['idclassannesco'])) ? $_GET['idclassannesco'] : null;
            $idsession = (count($_GET) > 0 && isset($_GET['idsession'])) ? $_GET['idsession'] : null;
            $rk = " SELECT ca.* , CONCAT(libelle, ' ', COALESCE(groupe, '')) libclas, CONCAT(sigle, '  ', COALESCE(groupe, '')) libclasabrev   ";
            $rk .= ", COALESCE(COUNT(CASE WHEN p.idsexe = 7 THEN 1 END),0) AS nbG ";
            $rk .= ", COALESCE(COUNT(CASE WHEN p.idsexe = 8 THEN 1 END),0) AS nbF ";

            $rk .= " FROM  classannescos ca, classetypes c, inscriptions ins, apprenants a, personnes p ";
            $rk .= " WHERE ca.idclasse = c.id ";
            $rk .= " AND ins.idclassannesco = ca.id ";
            $rk .= " AND ins.idapprenant = a.id ";
            $rk .= " AND a.idpersonne = p.id ";
            $rk .= " AND ca.id = '" . $idclassannesco . "' ";

            $laclannesco = $idclassannesco != null ? collect(DB::select($rk))->first() : null;
            $lasession = $idsession != null ? collect(DB::select(" SELECT * FROM sessionacademiques WHERE id = '" . $idsession . "' "))->first() : null;

            // dd($laclannesco);

            $config = session('config') != null ? session('config') : '';
            // $donneeimprim = session('donneeimprim') != null ? session('donneeimprim') : array();
            $dbulletin = session('dbulletin') != null ? session('dbulletin') : array();
            //  dd($dbulletin);

            $letitre = (count($_GET) > 0 && isset($_GET['letitre'])) ? $_GET['letitre'] : 'Titre document';
            $fichier = $letitre . '.pdf';

            $frmat = 'A4';
            $pag = in_array($config, array('moyenne-periode-apprenant')) ? 'landscape' : 'portrait';

            $pdf = PDF::loadView('print.bulletin', compact(
                'dbulletin',
                'labonne',
                'lannesco',
                'laclannesco',
                'lasession',


            ))->setPaper($frmat, $pag);
            $pdf->setBasePath(public_path());

            // return $pdf->stream();             ->setPaper('a4', 'landscape')
            return $pdf->stream($fichier, ['Content-Disposition' => 'inline']);

            // return view('print.cartes', compact(
            //     'donnees',
            //     'labonne'
            // ));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back();
        }

    }

    //recuTransfertDepot
    
    public function recuTransfertDepot(Request $request, $id)
    {
        $request['idenreg'] = $id;
        $request['casrecu'] = 'recudepot';
        return $this->recuTransfert($request,$id);
    }
    
    public function recuTransfertRetrait(Request $request, $id)
    {
        $request['idenreg'] = $id;
        $request['casrecu'] = 'recuretrait';
        return $this->recuTransfert($request,$id);
    }

public function recuTransfert(Request $request, $id){
  try {
    // dd($request);
            // $idabonnement = (count($_GET) > 0 && isset($_GET['idabonnement'])) ? $_GET['idabonnement'] : (session('idabonnement') != null ? session('idabonnement') : session('idabonnementEncours'));
            // $labonne = $idabonnement != null ? collect(DB::select(" SELECT * FROM abonnements WHERE id = '" . $idabonnement . "' "))->first() : null;
            // $idanneescolaire = (count($_GET) > 0 && isset($_GET['idanneescolaire'])) ? $_GET['idanneescolaire'] : null;
            // $lannesco = $idanneescolaire != null ? collect(DB::select(" SELECT a.*, CONCAT(andebut,' - ',(andebut + 1)) libannee FROM anneescolaires a WHERE id = '" . $idanneescolaire . "' "))->first() : null;
            // $donneeimprim = session('donneeimprim') != null ? session('donneeimprim') : array();
            
            $transfert =  transfertParCritere(" WHERE t.id = '".$id."' ")->first();
            $config = session('config') != null ? session('config') : '';
            $module = $request['casrecu'];
            $letitre = (count($_GET) > 0 && isset($_GET['letitre'])) ? $_GET['letitre'] : 'Titre document';
            $fichier = $letitre . '.pdf';

            $frmat = 'A4';
            $pag = in_array($config, array('moyenne-periode-apprenant')) ? 'landscape' : 'portrait';

            $pdf = PDF::loadView('back.transfert-recu', compact(
                'transfert', 'module'              


            ))->setPaper($frmat, $pag);
            $pdf->setBasePath(public_path());
            // return $pdf->stream();             ->setPaper('a4', 'landscape')
            return $pdf->stream($fichier, ['Content-Disposition' => 'inline']);
           
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back();
        }

}

}
