<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class moyentransfert extends Model
{
    protected $table = 'moyentransferts';
    protected $fillable = ['libelle', 'numero'];
}
