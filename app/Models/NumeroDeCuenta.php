<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumeroDeCuenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id', 'banco', 'numero_cuenta', 'rif_cedula', 'telefono', 'pago_movil',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}
