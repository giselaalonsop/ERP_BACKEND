<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaDeVenta extends Model
{
    use HasFactory;
    protected $table = 'forma_de_ventas';

    protected $fillable = [
        'nombre',
    ];
}
