<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transfert extends Model
{
    //

    public function expediteur()
    {
        // return $this->belongsTo(acteur::class, 'expediteur');
        return $this->belongsTo(acteur ::class, 'id expediteur', 'id');
    }

    public function destinataire()
    {
        // return $this->belongsTo(acteur::class, 'destinataire');
        return $this->belongsTo(acteur ::class, 'idrecepteur', 'id');
    }

    public function depart()
    {
        // return $this->belongsTo(adresse::class, 'depart');
        return $this->belongsTo(adresse ::class, 'iddepart', 'id');
    }

    public function destination()
    {
        // return $this->belongsTo(adresse::class, 'destination');
        return $this->belongsTo(adresse ::class, 'iddestination', 'id');
    }

    public function moyen()
    {
        // return $this->belongsTo(moyentransfert::class, 'moyen');
        return $this->belongsTo(moyentransfert ::class, 'idmoyentransfert', 'id');
    }

    public function caisse()
    {
        // return $this->belongsTo(caisse::class, 'caisse');
        return $this->belongsTo(caisse ::class, 'idcaisse', 'id');
    }

    public function etat()
    {
        // return $this->belongsTo(element::class, 'etat');
        return $this->belongsTo(element ::class, 'idetat', 'id');
    }

    public function trajet()
    {
        $trajet = "$this->depart->libelle - $this->destination->libelle";
        return $trajet;        
    }

}
