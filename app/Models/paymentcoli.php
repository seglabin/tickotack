<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class paymentcoli extends Model
{
    protected $table = 'paymentcolis';

    protected $fillable = [
        'payment_date',
        'montant',
        'idcolis',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'montant' => 'float',
    ];
}
