<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CierreDeCaja extends Model
{
    use HasFactory;

    protected $fillable = [
        'monto_total', 'dol_efectivo', 'zelle', 'bs_efectivo', 'bs_punto_de_venta', 'bs_pago_movil', 'fecha', 'usuario_id', 'estado', 'ubicacion'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
