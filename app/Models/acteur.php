<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class acteur extends Model
{
    protected $table = 'acteurs';
    protected $fillable = ['nom', 'tel', 'whatsap', 'marque', 'carte', 'idadreslivr'];

    public function adresse()
    {
        return $this->belongsTo(adresse::class, 'idadreslivr');
    }
}
