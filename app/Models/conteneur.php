<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class conteneur extends Model
{
    protected $table = 'conteneurs';

    protected $fillable = [
        'libelle',
        'numconteneur',
        'typeconteneur',
        'coutcfa',
        'lienconteneur',
        'idconsignataire',
        'idtypenvoi',
        'idmoyentransport',
        'iddepart',
        'iddestination',
        'datedepart',
        'douane',
        'poidstotal',
    ];
}
