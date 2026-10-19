<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class adresse extends Model
{
    protected $table = 'adresses';
    protected $fillable = ['libelle', 'pays', 'devise'];
}
