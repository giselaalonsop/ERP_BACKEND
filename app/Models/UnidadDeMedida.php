<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadDeMedida extends Model
{
    use HasFactory;
    protected $table = 'unidad_de_medidas';

    protected $fillable = [
        'nombre',
        'abreviacion',
    ];
}
