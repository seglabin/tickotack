<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class histoconteneur extends Model
{
    protected $table = 'histoconteneurs';

    protected $fillable = [
        'datetat',
        'observation',
        'ideconteneur',
        'idetat',
    ];
}
