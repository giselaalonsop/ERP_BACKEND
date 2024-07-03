<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id', 'usuario_id', 'fecha', 'monto_total', 'monto_abonado', 'monto_restante', 'estado',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function abonar($monto)
    {
        $this->monto_abonado += $monto;
        $this->monto_restante = $this->monto_total - $this->monto_abonado;
        if ($this->monto_restante <= 0) {
            $this->estado = 'pagada';
        }
        $this->save();
    }
}
