<?php

// app/Models/Venta.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente', 'usuario', 'fecha', 'numero_de_venta',  'comprobante', 'estado', 'mayor_o_detal',
        'location', 'total_venta_bs', 'metodo_pago', 'total_venta_dol', 'descuento'
    ];
    public static $metodoPagoEnum = [
        'dol_efectivo',
        'bs_punto_de_venta',
        'bs_pago_movil',
        'zelle',
        'bs_efectivo',
        'pagar_luego'
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }
}
