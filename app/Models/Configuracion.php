<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $fillable = [
        'IVA', 'porcentaje_ganancia', 'nombre_empresa', 'telefono', 'rif', 'correo', 'numero_sucursales', 'direcciones', 'pago_movil', 'transferencias', 'logo',
    ];

    protected $casts = [
        'direcciones' => 'array', // Para manejar el campo JSON como array en Eloquent
        'pago_movil' => 'array', // Para manejar el campo JSON como array en Eloquent
        'transferencias' => 'array', // Para manejar el campo JSON como array en Eloquent
    ];
}
