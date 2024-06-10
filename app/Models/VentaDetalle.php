<?php

// app/Models/VentaDetalle.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id', 'codigo_barras', 'nombre', 'cantidad', 'precio_unitario', 'total'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
