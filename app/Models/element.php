<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class element extends Model
{
    protected $fillable = [
        'libelle',
        'nomtable',
        'lecas',
        'valeur',
    ];
}
