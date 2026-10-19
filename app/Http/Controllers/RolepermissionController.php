<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;
use App\Models\Rolepermission;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;


class RolepermissionController extends Controller
{

    public function index(Request $request)
    {
        // dd($request);
        //  dd($_GET);
        try {
            $rekete = "";
            $donnees = role::orderBy('name')->get();
            $id = (isset($_GET['idenreg']) && $_GET['idenreg'] != null) ? $_GET['idenreg'] : (session('idenreg') != null ? session('idenreg') : null);

            $module = (isset($_GET['module']) && $_GET['module'] != null) ? $_GET['module'] : 'listeProfil'; //(session('module') != null ? session('module') : 'listeProfil');
            // dd($module);
            $lenregistrement = ($id != null) ? role::find($id) : null;
            switch ($module) {
                case 'adddroitacces':
                    $rekete = " SELECT * FROM permissions WHERE id NOT IN (SELECT idpermission FROM rolepermissions WHERE idrole = '" . $id . "') ORDER BY CASE WHEN idparent IS NULL THEN id ELSE idparent END, idparent IS NOT NULL, libelle ";
                    break;
                case 'detailsdroitacces':
                    $rekete = " SELECT * FROM permissions WHERE id IN (SELECT idpermission FROM rolepermissions WHERE idrole = '" . $id . "')  ORDER BY CASE WHEN idparent IS NULL THEN id ELSE idparent END, idparent IS NOT NULL, libelle ";
                    break;
            }

            if ($rekete != "")
                $donnees = collect(DB::select($rekete));

            //  dd($rekete);
            $idenreg = $id;
            session(['module' => $module]);
            session(['config' => 'rolepermission']);

            return view('back.droitsAcces', compact(
                'donnees',
                'module',
                'lenregistrement',
                'idenreg'
            ));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('rolepermission')->with('error', 'Une erreur est survenue lors du chargement de la page.');
        }
    }


    public function validerDroitsAcces(Request $request)
    {
        //  dd($_GET);
        try {
            //taille	idenreg(idrole)	idpermission0	chk0


            $module = $_GET['module'];
            $idrole = $_GET['idenreg'];
            $n = isset($_GET['taille']) ? $_GET['taille'] : 0;
            for ($i = 0; $i < $n; $i++) {
                // $chk = 'chk' . $i;
                if (isset($_GET['chk' . $i]) && $_GET['chk' . $i] == true) {
                    // $idperm = 'idpermission' . $i;
                    $idpermission = $_GET['idpermission' . $i];
                    $rp = rolepermission::where('idrole', $idrole)
                        ->where('idpermission', $idpermission)
                        ->first();
                    // dd($idrole.' '.$idpermission.' ' .$rp);  
                    switch ($module) {
                        case 'adddroitacces':
                            if ($rp == null) {
                                $e = new rolepermission();
                                $e->idrole = $idrole;
                                $e->idpermission = $idpermission;
                                $e->save();

                                // Auto-add parent if it exists
                                $perm = \App\Models\permission::find($idpermission);
                                if ($perm && $perm->idparent) {
                                    $parentRp = rolepermission::where('idrole', $idrole)
                                        ->where('idpermission', $perm->idparent)
                                        ->first();
                                    if ($parentRp == null) {
                                        $p = new rolepermission();
                                        $p->idrole = $idrole;
                                        $p->idpermission = $perm->idparent;
                                        $p->save();
                                    }
                                }
                            }

                            break;
                        case 'retraitdroitacces':
                            if ($rp != null) {
                                $rp->delete();

                                // Also remove children if this is a parent? 
                                // The request didn't explicitly ask for auto-removal, 
                                // but usually it's desired. Let's stick to the request for now.
                            }

                            break;
                    }
                }
            }

            $module = 'listeProfil';
            session(['module' => $module]);
            session(['config' => 'rolepermission']);

            $u = Auth::user();
            if ($u != null) {
                $rekmenu = "SELECT DISTINCT idpermission FROM rolepermissions WHERE idrole = '" . $u->idrole . "' ";
                $menusUser = collect(DB::select($rekmenu))->toArray();
                $menusUser = array_column($menusUser, 'idpermission');
                session(['menusUser' => $menusUser]);
            }


            return redirect()->route('rolepermission');
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('rolepermission')->with('error', 'Une erreur est survenue lors de l\'enregistrement de la classe année scolaire.' . $e);
        }
    }
}
