<?php


// app/Models/Venta.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente', 'usuario', 'fecha', 'numero_de_venta', 'total_venta', 'comprobante', 'estado', 'mayor_o_detal'
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }
}
