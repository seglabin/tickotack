<?php

use Illuminate\Support\Facades\DB;
use App\Models\element;

use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SpecificCellImport;

function chargerComboa($donnees, $idtable, $colonne, $idcombo, $choixdefaut = "Choisir...", $largeur = "210", $onchange = "", $idencours = "", $multiple = "", $idtabloRet = "")
{
    $affich = "";
    $onch = "";
    $largeur = ($largeur == '') ? '100%' : $largeur . 'px';

    if ($onchange != '')
        $onch = "onchange =" . $onchange; //."'";
    if ($multiple == "") {
        $affich .= "<select class='form-control select2' id='" . $idcombo . "' name='" . $idcombo . "' " . $onch . " style=' width:" . $largeur . ";' >";
    } else {
        $affich .= "<select class='form-control select2' multiple='" . $multiple . "' id='" . $idcombo . "'  name='" . $idtabloRet . "' " . $onch . " style='height: 30px; width: " . $largeur . "px;' >";
    }
    $affich .= "<option value='0'>--" . $choixdefaut . "--</option>";

    if (sizeof($donnees) > 0) {
        foreach ($donnees as $list) {
            // dd($list);
            $depinit = "";
            if ($multiple == "") {
                if ($list[$idtable] == $idencours) {
                    $depinit = 'selected=selected';
                }
            } else {
                if (is_array($idencours) ? in_array($list[$idtable], $idencours) : $list[$idtable] == $idencours) {
                    $depinit = 'selected=selected';
                }
            }

            $affich .= "<option " . $depinit . " value=" . $list[$idtable] . ">";
            $affich .= $list[$colonne] . "</option>";
        }
    }
    $affich .= "</select>";
    return $affich;
}
function chargerCombo($donnees, $idtable, $colonne, $idcombo, $choixdefaut = "Choisir...", $largeur = "210", $onchange = "", $idencours = "", $disabled = '', $multiple = "", $idtabloRet = "")
{
    $affich = "";
    $onch = "";
    $largeur = ($largeur == '') ? '100%' : $largeur . 'px';

    if ($onchange != '')
        $onch = "onchange =" . $onchange; //."'";
    if ($multiple == "") {
        $affich .= "<select class='form-control rounded-4 select2' " . $disabled . "  id='" . $idcombo . "' name='" . $idcombo . "' " . $onch . " style=' width:" . $largeur . ";' >";
    } else {
        $affich .= "<select class='form-control rounded-4 selects' multiple='" . $multiple . "' id='" . $idcombo . "'  name='" . $idtabloRet . "' " . $onch . " style='height: 30px; width: " . $largeur . "px;' >";
    }
    $affich .= "<option value='0'>--" . $choixdefaut . "--</option>";

    if (sizeof($donnees) > 0) {
        foreach ($donnees as $list) {
            // dd($list);
            $depinit = "";
            if ($multiple == "") {
                if ($list->$idtable == $idencours) {
                    $depinit = 'selected=selected';
                }
            } else {
                if (is_array($idencours) ? in_array($list->$idtable, $idencours) : $list->$idtable == $idencours) {
                    $depinit = 'selected=selected';
                }
            }

            $affich .= "<option " . $depinit . " value=" . $list->$idtable . ">";
            $affich .= $list->$colonne . "</option>";
        }
    }
    $affich .= "</select>";
    return $affich;
}
function chargerComboSimple($donnees, $idtable, $colonne, $idcombo, $choixdefaut = "Choisir...", $largeur = "210", $onchange = "", $idencours = "", $multiple = "", $idtabloRet = "")
{
    $affich = "";
    $onch = "";
    $largeur = ($largeur == '') ? '100%' : $largeur . 'px';

    if ($onchange != '')
        $onch = "onchange =" . $onchange; //."'";
    if ($multiple == "") {
        $affich .= "<select class='form-control rounded-4 ' id='" . $idcombo . "' name='" . $idcombo . "' " . $onch . " style=' width:" . $largeur . ";' >";
    } else {
        $affich .= "<select class='form-control rounded-4 ' multiple='" . $multiple . "' id='" . $idcombo . "'  name='" . $idtabloRet . "' " . $onch . " style='height: 30px; width: " . $largeur . "px;' >";
    }
    $affich .= "<option value='0'>--" . $choixdefaut . "--</option>";

    if (sizeof($donnees) > 0) {
        foreach ($donnees as $list) {
            // dd($list);
            $depinit = "";
            if ($multiple == "") {
                if ($list->$idtable == $idencours) {
                    $depinit = 'selected=selected';
                }
            } else {
                if (is_array($idencours) ? in_array($list->$idtable, $idencours) : $list->$idtable == $idencours) {
                    $depinit = 'selected=selected';
                }
            }

            $affich .= "<option " . $depinit . " value=" . $list->$idtable . ">";
            $affich .= $list->$colonne . "</option>";
        }
    }
    $affich .= "</select>";
    return $affich;
}

function infos($msg)
{
?>
    <script>
        alert('<?php echo addslashes($msg) ?>');
    </script>

    <?php
}

function sansespace($ch)
{
    return str_replace(' ', '', $ch);
}

function elementParTable($nomtable)
{
    return element::where('nomtable', $nomtable)->orderBy('libelle')->get();
}

function infoTableParId($table, $id, $colonne = '')
{
    $sql = "SELECT * FROM $table WHERE id = '" . $id . "'";
    $res = collect(DB::select($sql));
    // dd($res[0]);
    if (count($res) > 0) {
        if ($colonne != '') {
            return ($res[0])->$colonne;
        } else {
            return $res[0];
        }
    } else {
        return null;
    }
}

function infoParRekete($rekete, $colonne = '')
{
    $res = collect(DB::select($rekete));
    // dd($res[0]);
    if (count($res) > 0) {
        if ($colonne != '') {
            return ($res[0])->$colonne;
        } else {
            return $res[0];
        }
    } else {
        return null;
    }
}




function rechercheSouchaine($chaine, $souchaine)
{
    if (strpos($chaine, $souchaine) !== FALSE) {
        return TRUE;
    }
    return FALSE;
}

function formatDateFrancais($date)
{
    $dateObj = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    if ($dateObj) {
        return $dateObj->format('d-m-Y H:i:s');
    }
    return '';
}



function appreciation($note)
{
    $note = ($note == 0) ? 0.0 : $note;

    if (doubleval($note) < doubleval(0)) {
        return '';
    } elseif (doubleval($note) == doubleval(0)) {
        return 'NULL';
    } elseif (doubleval($note) < doubleval(6)) {
        return 'MÉDIOCRE';
    } elseif (doubleval($note) < doubleval(8)) {
        return 'FAIBLE';
    } elseif (doubleval($note) < doubleval(10)) {
        return 'INSUFFISANT';
    } elseif (doubleval($note) < doubleval(12)) {
        return 'PASSABLE';
    } elseif (doubleval($note) < doubleval(14)) {
        return 'ASSEZ BIEN';
    } elseif (doubleval($note) < doubleval(16)) {
        return 'BIEN';
    } elseif (doubleval($note) < doubleval(18)) {
        return 'TRÈS BIEN';
    } else {
        return 'EXCELLENT';
    }
}

function idadresseCaisseUser()
{
    $u = \Illuminate\Support\Facades\Auth::user();
    $idcaisseuser = $u->idcaisse ?? null;
    $idadrescaisseuser = $idcaisseuser != null ? infoTableParId('caisses', $idcaisseuser, 'idadresse') : null;
    return $idadrescaisseuser;
}

function tableau_html($t, $liste_entete, $liste_colonne, $btn = "", $num = true, $class = "")
{

    $idadrescaisseuser = idadresseCaisseUser();
    $iduserRole = \Illuminate\Support\Facades\Auth::user()->idrole ?? null;


    $config = session('config');
    $cfmodif = 'modifier-' . $config;
    $cfsuppr = 'supprimer-' . $config;

    $th = explode('|', $liste_entete);
    $tc = explode('|', $liste_colonne);

    if (count($th) != count($tc)) {
        echo '<script>alert("Le nombre de colonnes d\'entête doit être égal au nombre de colonnes de données!");</script>';
        return;
    }

    $html = '<div class="table-responsive card-body">';
    $html .= '<table id="example1" class="table table-bordered table-striped tableDT" style="width:100%; border-collapse:collapse;">';

    // En-tête
    $html .= '<thead><tr>';
    if ($num) {
        $html .= '<th style="width:20px;">N°</th>';
    }
    foreach ($th as $e) {
        $html .= '<th>' . $e . '</th>';
    }
    if ($btn != '') {
        $html .= '<th>Actions</th>';
    }
    $html .= '</tr></thead>';

    // Corps
    $html .= '<tbody>';
    foreach ($t as $i => $v) {
        $id = is_array($v) ? ($v['id'] ?? '') : ($v->id ?? '');
        $dataln = $id;
        $html .= '<tr>';
        if ($num) {
            $html .= '<td>' . ($i + 1) . '</td>';
        }
        $maj = is_array($v) ? ($v['maj'] ?? '') : ($v->maj ?? '');
        foreach ($tc as $e) {
            $val = is_array($v) ? ($v[$e] ?? '') : ($v->$e ?? '');
            $dataln .= '|' . $val;
            $html .= '<td>' . formatervaleur($val) . '</td>';
        }
        if ($btn != '') {
            $html .= '<td style="white-space:nowrap;">';

            if ((strpos($btn, 'retraitransfert') !== false) && ($v->idetat == 1)) {
                $iddestination = is_array($v) ? ($v['iddestination'] ?? '') : ($v->iddestination ?? '');
                if (($iddestination == $idadrescaisseuser) || ($iduserRole == 1)) {
                    $html .= '<input type="hidden" id="dataln' . $i . '" name="dataln' . $i . '" value="' . $dataln . '">';
                    $html .= '<a href="#" class="btn btn-info btn-sm" onclick="ouvrirModalRetrait(\'' . $id . '\');" style="margin:0 2px; padding:6px 10px; font-size:14px;" title="Retrait de transfert" ><i class="fa fa-credit-card"></i></a>';
                }   
                
            }

            if (strpos($btn, 'recu') !== false && $config == 'transfert') {
                if($maj == '1' || $maj == ''){
                    $html .= '<a href="/transfert/' . $id . '/recu-depot-transfert" target="_blank" class="btn btn-secondary btn-sm" style="margin:0 2px; padding:6px 10px; font-size:14px;" title="Imprimer le reçu Dépôt"><i class="fa fa-print"></i></a>';
                }else{                    
                    $html .= '<a href="/transfert/' . $id . '/recu-retrait-transfert" target="_blank" class="btn btn-secondary btn-sm" style="margin:0 2px; padding:6px 10px; font-size:14px;" title="Imprimer le reçu -Retrait "><i class="fa fa-print"></i></a>';
                }           
            }

            if ($config == 'expedition') {
                if (strpos($btn, 'historique') !== false) {
                    $html .= '<a href="#" class="btn btn-secondary btn-sm" onclick="ouvrirHistoriqueExpedition(\'' . $id . '\'); return false;" style="margin:0 2px; padding:6px 10px; font-size:14px;" title="Historique du colis" aria-label="Historique du colis"><i class="fa fa-history"></i></a>';
                }
                $actionsExpedition = [
                    'envoicolis' => ['Envoi colis', 'fa-truck', 'btn-primary', 2],
                    'passagedouane' => ['Passage douane', 'fa-stamp', 'btn-warning', 13],
                    'arrivemagasin' => ['Arrivée magasin', 'fa-store', 'btn-success', 14],
                    'retraitcolis' => ['Retrait colis', 'fa-box-open', 'btn-info', 15],
                    // 'retraitmagasin' => ['Retrait magasin', 'fa-hand-holding', 'btn-secondary'],
                ];
                foreach ($actionsExpedition as $action => [$libelle, $icone, $classe, $val]) {
                    $actionDisponible = $action === 'retraitcolis'
                        ? mb_strtolower(trim((string) ($v->etat ?? ''))) === 'arrivé au magasin'
                        : (($v->idetat + 1) == $val);
                    if ((strpos($btn, $action) !== false) && $actionDisponible) {
                        $details = '';
                        if ($action === 'retraitcolis') {
                            $details = htmlspecialchars(json_encode([
                                'code' => $v->code ?? '',
                                'datenreg' => $v->datenreg ?? '',
                                'expediteur' => $v->expediteur ?? '',
                                'recepteur' => $v->recepteur ?? '',
                                'trajet' => $v->trajet ?? '',
                                'typecolis' => $v->typecolis ?? '',
                                'conteneur' => $v->conteneur ?? '',
                                'nbrcolis' => $v->nbrcolis ?? '',
                                'poids' => $v->poids ?? '',
                                'montant' => $v->montant ?? '',
                                'marque' => $v->marque ?? '',
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                            $details = ' data-expedition="' . $details . '"';
                        }
                        $html .= '<a href="#" class="btn ' . $classe . ' btn-sm"' . $details . ' onclick="ouvrirModalHistoexpedition(\'' . $id . '\', \'' . $action . '\', this); return false;" style="margin:0 2px; padding:6px 10px; font-size:14px;" title="' . $libelle . '"><i class="fa ' . $icone . '"></i></a>';
                    }
                }
            }
            if ($config == 'conteneur') {
                if (strpos($btn, 'details') !== false) {
                    $html .= '<a href="#" class="btn btn-info btn-sm" onclick="afficherExpeditionsConteneur(\'' . $id . '\'); return false;" style="margin:0 2px; padding:6px 10px; font-size:14px;" title="Détails des expéditions" aria-label="Détails des expéditions"><i class="fa fa-eye"></i></a>';
                }
                $actionsConteneur = [
                    'envoyer' => ['Envoyer', 'fa-truck', 'btn-primary', ''],
                    'passagedouane' => ['Passage douane', 'fa-stamp', 'btn-warning', 'envoyé'],
                    'arriveemagasin' => ['Arrivée au magasin', 'fa-store', 'btn-success', 'passage douane'],
                ];
                $etatConteneur = mb_strtolower(trim((string) ($v->etat ?? '')));
                foreach ($actionsConteneur as $action => [$libelle, $icone, $classe, $etatPrecedent]) {
                    if (strpos($btn, $action) !== false && $etatConteneur === $etatPrecedent) {
                        $html .= '<a href="#" class="btn ' . $classe . ' btn-sm" onclick="ouvrirModalHistoconteneur(\'' . $id . '\', \'' . $action . '\'); return false;" style="margin:0 2px; padding:6px 10px; font-size:14px;" title="' . $libelle . '"><i class="fa ' . $icone . '"></i></a>';
                    }
                }
            }
            if ((strpos($btn, 'modifier') !== false) && ($maj == '1' || $maj == '')) {
                $html .= '<a href="#" class="btn btn-warning btn-sm" onclick="modifier(\'' . $cfmodif . '\',\'' . $id . '\');" style="margin:0 2px; padding:6px 10px; font-size:14px;" title="Modifier"><i class="fa fa-edit"></i></a>';
            }
            if ((strpos($btn, 'supprimer') !== false) && ($maj == '1' || $maj == '')) {
                $html .= '<a href="#" class="btn btn-danger btn-sm" onclick="supprimer(\'' . $cfsuppr . '\',\'' . $id . '\');" style="margin:0 2px; padding:6px 10px; font-size:14px;" title="Supprimer"><i class="fa fa-trash"></i></a>';
            }
            if (strpos($btn, 'reinitialiser') !== false) {
                $html .= '<a href="#" class="btn btn-secondary btn-sm" onclick="reinitialiser(\'' . $id . '\');" style="margin:0 2px; padding:6px 10px; font-size:14px;" title="Réinitialiser le mot de passe"><i class="fa fa-key"></i></a>';
            }
           
            $html .= '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table></div>';

    echo $html;
}


function saveFichier(Request $request, $nomfichier, $cheminsauvegarde, $prefix = '')
{
    $imagePath = null;  // chemin sauvegarde  = 'storage/images/abonnements'
    $customFileName = null;
    //  dd($request->hasFile($nomfichier));
    if ($request->hasFile($nomfichier)) {
        $imagePath = $request->file($nomfichier);
        $customFileName = $imagePath->getClientOriginalName(); // Utilisez le nom d'origine du fichier
        if ($prefix != '') {
            $customFileName = uniqid($prefix) . uniqid() . '.' . $request->$nomfichier->extension();
        }
        $imagePath->move(public_path($cheminsauvegarde), $customFileName);
    }
    //  dd($customFileName);
    return $customFileName;
}


function rechercherElement($table, $colonne, $valeur)
{
    $cols = array_column($table, $colonne);
    $index = array_search($valeur, $cols);
    return $index;
}

function formatMontant($mont)
{
    return number_format($mont, 2, ',', ' ');
}

function chaineGauche($ch, $sousch)
{
    $t = strlen($ch) - strlen($sousch);
    return substr($ch, 0, $t);
}

function debutMois($mois = '', $annee = '')
{
    if ($mois == '') $mois = date('m');
    if ($annee == '') $annee = date('Y');
    return date("Y-m-d", mktime(0, 0, 0, $mois, 1, $annee));
}

function finMois($mois = '', $annee = '')
{
    if ($mois == '') $mois = date('m');
    if ($annee == '') $annee = date('Y');
    return date("Y-m-d", mktime(0, 0, 0, $mois + 1, 0, $annee));
}

function periodeTrimestre($n, $an = '')
{
    $period = array();
    $m1  = ($n - 1) * 3 + 1;
    $m2 = $m1 + 1;
    $m3 = $m2 + 1;
    $period[0]['debut'] = debutMois($m1, $an);
    $period[0]['fin'] = finMois($m1, $an);
    $period[1]['debut'] = debutMois($m2, $an);
    $period[1]['fin'] = finMois($m2, $an);
    $period[2]['debut'] = debutMois($m3, $an);
    $period[2]['fin'] = finMois($m3, $an);
    return $period;
}

function trimestreEnCours()
{
    $mois = date('m');
    if ($mois <= 3) {
        return 1;
    } elseif ($mois <= 6) {
        return 2;
    } elseif ($mois <= 9) {
        return 3;
    } else {
        return 4;
    }
}

function donneePeriode($type)
{
    $d = array();
    switch ($type) {
        case 'T':
            //  $d[]= ['id'=> 0,'libelle'=> '1er Trimestre' ];
            //  $d[]= ['id'=> 1,'libelle'=> '2ème Trimestre' ];
            $d[0]['id'] = 1;
            $d[0]['libelle'] = '1er Trimestre';
            $d[0]['lesmois'] = 'Janvier|Février|Mars';
            $d[1]['id'] = 2;
            $d[1]['libelle'] = '2eme Trimestre';
            $d[1]['lesmois'] = 'Avril|Mai|Juin';
            $d[2]['id'] = 3;
            $d[2]['libelle'] = '3eme Trimestre';
            $d[2]['lesmois'] = 'Juillet|Août|Septembre';
            $d[3]['id'] = 4;
            $d[3]['libelle'] = '4eme Trimestre';
            $d[3]['lesmois'] = 'Octobre|Novembre|Décembre';
            break;
        case 'S':
            $d[0]['id'] = 0;
            $d[0]['libelle'] = '1er Semestre';
            $d[1]['id'] = 1;
            $d[1]['libelle'] = '2eme Semestre';
            break;
        case 'M':
            $d[0]['id'] = 1;
            $d[0]['libelle'] = 'Janvier';
            $d[1]['id'] = 2;
            $d[1]['libelle'] = 'Février';
            $d[2]['id'] = 3;
            $d[2]['libelle'] = 'Mars';
            $d[3]['id'] = 4;
            $d[3]['libelle'] = 'Avril';
            $d[4]['id'] = 5;
            $d[4]['libelle'] = 'Mai';
            $d[5]['id'] = 6;
            $d[5]['libelle'] = 'Juin';
            $d[6]['id'] = 7;
            $d[6]['libelle'] = 'Juillet';
            $d[7]['id'] = 8;
            $d[7]['libelle'] = 'Août';
            $d[8]['id'] = 9;
            $d[8]['libelle'] = 'Septembre';
            $d[9]['id'] = 10;
            $d[9]['libelle'] = 'Octobre';
            $d[10]['id'] = 11;
            $d[10]['libelle'] = 'Novembre';
            $d[11]['id'] = 12;
            $d[11]['libelle'] = 'Décembre';
            break;

        default:
            # code...
            break;
    }
    return $d;
    // return collect($d);
}

function lesdatesdumois($mois, $annee)
{
    $rekete = "SELECT * FROM datesdumois WHERE mois = '" . $mois . "' AND annee = '" . $annee . "'";
    return collect(DB::select($rekete));
}

function enregistreOperationFinanciere($sens, $montant, $idsrce, $libsrce, $idcaisse, $id = null)
{
    //`sens`, `montant`, `idsrce`, `libsrce`, `idcaisse`
    $operation = $id == null ? new App\Models\OperationFinanciere() :  App\Models\OperationFinanciere::find($id);
    $operation->sens = $sens;
    $operation->montant = $montant;
    $operation->idsrce = $idsrce;
    $operation->libsrce = $libsrce;
    $operation->idcaisse = $idcaisse;
    if ($operation->save()) {
        $caisse = App\Models\caisse::find($idcaisse);
        $solde = soldeCaisse($idcaisse);
        $caisse->montant = $solde;
        $caisse->save();
    }
}

function soldeCaisse($idcaisse)
{
    $ent = "SELECT SUM(montant) as total FROM operationfinancieres WHERE sens = 'Entrée' AND idcaisse = '" . $idcaisse . "'";
    $sor = "SELECT SUM(montant) as total FROM operationfinancieres WHERE sens = 'Sortie' AND idcaisse = '" . $idcaisse . "' ";
    return (collect(DB::select($ent))->first()->total ?? 0) - (collect(DB::select($sor))->first()->total ?? 0);
}

function formatervaleur($valeur, $sepMilli = ' ')
{
    if (is_numeric($valeur)) {
        // return number_format($valeur, 2, $sepDeci, $sepMilli);
        if ($valeur == null || $valeur == '') {
            return 0;
        } else {
            return formatnombre($valeur);
        }
    } elseif (estDateOuDatetime($valeur)) {
        return formaterDateIntelligente($valeur);
    }
    return $valeur;
}
function formatnombre($nombre, $separateur = " ")
{
    $nombre = '' . $nombre . '';
    $n = strlen($nombre);
    $ch = "";
    $f = 0;
    while ($n > 0) {

        $n--;
        if ($f == 3) {               //Functions::afficheBoiteMsg($ch);
            $ch = $separateur . $ch;
            $f = 0; //echo '   M  '.$n.' = '. $ch;
        }
        $ch = $nombre[$n] . $ch;
        $f++;
    }

    //        echo '  M'.$ch.'H  '.$nombre;
    return $ch;
}


function formaterDateIntelligente($valeur)
{
    $dateObj = new DateTime($valeur);
    // Vérifie si l'heure est à minuit (00:00:00)
    if ($dateObj->format('H:i:s') === '00:00:00') {
        return $dateObj->format('d/m/Y'); // Format date seule
    }

    // Sinon, retourne la date et l'heure
    return $dateObj->format('d/m/Y H:i'); // Format datetime
}


function estDateOuDatetime($valeur)
{
    // Teste le format complet avec l'heure (Datetime)
    $dti = DateTime::createFromFormat('Y-m-d H:i:s', $valeur);
    $errors = DateTime::getLastErrors();
    if ($dti && $errors == false) {
        return true; //'datetime';
    }

    // Teste le format simple sans l'heure (Date)
    $dt = DateTime::createFromFormat('Y-m-d', $valeur);
    $errors = DateTime::getLastErrors();

    if ($dt && $errors['warning_count'] == 0 && $errors['error_count'] == 0) {
        return  true; //'date';
    }

    return false; // Ce n'est ni l'un ni l'autre
}

function articleParCritere($critere = "")
{
    $rekete = " SELECT a.*, c.libelle libcategorie, COALESCE((SELECT libelle FROM marques WHERE id = a.idmarque),'') libmarque  ";
    $rekete .= " FROM articles a, categories c ";
    $rekete .= " WHERE c.id = a.idcategorie ";
    $rekete .= ($critere != '') ? " AND " . $critere : "";
    return  collect(DB::select($rekete));
}


function afficheArticle($id)
{


    $p = collect(DB::select("SELECT * FROM articles WHERE id = '" . $id . "' "))->first();
    // dd($p);
    if ($p != null) {


        $img1 = "storage/images/Articles/" . $p->photo1;
        $img2 = "storage/images/Articles/" . $p->photo2;
        $nom = $p->libelle;
        $description = $p->description;
        $prix = $p->prix;


    ?>
        <!-- <div class="product-layout product-grid col-lg-3 col-md-4 col-sm-6 col-xs-12"> -->
        <div class="product-layout col-xs-12">
            <div class="product-thumb transition">
                <div class="image"><a href="indexb8ca.html?route=product/product&amp;product_id=43"><img
                            src="<?php echo $img1; ?>" alt="<?php echo $nom; ?>" title="<?php echo $nom; ?>"
                            class="img-responsive center-block" /></a>

                    <a href="indexb8ca.html?route=product/product&amp;product_id=43"><img
                            src="<?php echo $img2; ?>" class="img-responsive second-img"
                            alt="hover image" /></a>

                </div>
                <div class="caption text-center">
                    <h4><a href="indexb8ca.html?route=product/product&amp;product_id=43"><?php echo $nom; ?></a></h4>
                    <p class="price">
                        <span data-update='price-43' class="price"><?php echo $prix; ?></span>
                    </p>
                    <div class="option-product">
                        <input type="hidden" name="product_id" value="43" />
                        <div class="form-group required ">
                            <label class="control-label" for="input-option232">Select</label>
                            <select name="option[232]" id="input-option232" class="insp-option-select form-control">
                                <option value=""> --- Please Select --- </option>
                                <option value="33">1 Pack
                                </option>
                                <option value="34">2 Pack
                                </option>
                                <option value="35">3 Pack
                                </option>
                            </select>
                        </div>


                    </div>


                    <div class="button-group">
                        <!-- <button type="button" data-toggle="tooltip" title="Add to Wish List" onclick="wishlist.add('43');"><svg
                                width="16px" height="16px">
                                <use xlink:href="#addwish"></use>
                            </svg></button> -->
                        <!-- <button type="button" data-toggle="tooltip" title="Compare this Product"
                            onclick="compare.add('43');"><svg width="16px" height="16px">
                                <use xlink:href="#addcompare"></use>
                            </svg></button>
                        <div class="bquickv" data-toggle="tooltip" title="Quick View"></div>
                        
                        <button class="btn btn-default"><i class="fa fa-heart"></i></button>
                        -->
                    </div>

                    <div>
                        <!-- <div class="homeqt d-inline-block">
                            <div class="homeqtinner">
                                <button type="button" onclick="qty.minus('43')"
                                    class="form-control pull-left btn-number btnminus dis-43" disabled="disabled">
                                    <span class="glyphicon glyphicon-minus"></span>
                                </button>
                                <input name="quantity" class="addhqty" type="text" value="2" size="2" min="1" max="999"
                                    id="input-quantity-43" class="form-control input-number pull-left" />
                                <button type="button" onclick="qty.plus('43')"
                                    class="form-control pull-left btn-number btnplus">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </button>
                            </div>
                        </div> -->
                        <?php
                        $idchpqte = uniqid('qte');
                        ?>
                        <div class="form-group">
                            <label class="control-label">Qty</label>
                            <div class="input-group" style="max-width:140px;">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" onclick="minusqte('<?php echo $idchpqte; ?>')">-</button>
                                </span>
                                <input type="text" value="1" id="<?php echo $idchpqte; ?>" class="form-control">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" onclick="plusqte('<?php echo $idchpqte; ?>')">+</button>
                                </span>
                            </div>
                        </div>
                        <div class="d-inline-block">
                            <!-- <input type="hidden" name="product_id" value="43" />
                            <button type="button" class="acart add-to-cart insp-cart"><i class="fa fa-shopping-basket"
                                    aria-hidden="true"></i></button> -->
                            <button class="btn btn-default"><i class="fa fa-heart"></i></button>
                            <button class="btn btn-default"><i class="fa fa-bar-chart"></i></button>
                            <button class="btn btn-primary" onclick="addcart('<?php echo $p->id; ?>', '<?php echo $idchpqte; ?>')"><i class="fa fa-shopping-basket" aria-hidden="true"></i> </button>

                        </div>
                        <input type="hidden" name="hid-qty-msg" value="">
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}

function transfertParCritere($critere = "")
{

$rekete = "
            SELECT t.id, t.code, t.datetransfert, t.datepayement, t.montdepot, t.montretrait, t.montpaye,
                t.idexpediteur, t.idrecepteur, t.iddepart, t.iddestination, t.idmoyentransfert,
                   t.taux, t.observation, t.idetat maj,
                   exp.nom AS expediteur, rec.nom AS recepteur, t.idetat ,
                   exp.carte cartexpediteur,rec.carte carte_recepteur,
                   dep.libelle AS villedepart, dest.libelle AS villedestination,
                   CONCAT(dep.libelle, ' - ',dest.libelle) AS trajet,
                   m.libelle AS moyen, c.libelle AS caisse, e.libelle AS etat
            FROM transferts t
            LEFT JOIN acteurs exp ON exp.id = t.idexpediteur
            LEFT JOIN acteurs rec ON rec.id = t.idrecepteur
            LEFT JOIN adresses dep ON dep.id = t.iddepart
            LEFT JOIN adresses dest ON dest.id = t.iddestination
            LEFT JOIN moyentransferts m ON m.id = t.idmoyentransfert
            LEFT JOIN caisses c ON c.id = t.idcaisse
            LEFT JOIN elements e ON e.id = t.idetat 
            " . $critere . "            
            ORDER BY t.datetransfert DESC
        ";
        // dd($rekete);
    $transferts = collect(DB::select($rekete)); //->first(); WHERE t.id = ?

    abort_unless($transferts, 404, 'Transfert introuvable.');
    return $transferts;
}

function loginActeur()
{
    $annee = now()->format('Y');
    $prefixe = $annee . 'CL';
    $logins = DB::table('users')
        ->where('username', 'like', $prefixe . '%')
        ->pluck('username');

    $numeroMax = 0;
    foreach ($logins as $login) {
        if (preg_match('/^' . $prefixe . '(\d+)$/', $login, $matches)) {
            $numeroMax = max($numeroMax, (int) $matches[1]);
        }
    }
    // dd($prefixe . str_pad((string) ($numeroMax + 1), 6, '0', STR_PAD_LEFT));
    return $prefixe . str_pad((string) ($numeroMax + 1), 6, '0', STR_PAD_LEFT);
}
