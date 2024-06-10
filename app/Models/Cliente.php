<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'apellido', 'correo_electronico', 'numero_de_telefono', 'direccion', 'cedula', 'edad', 'numero_de_compras', 'cantidad_de_articulos_comprados', 'estatus', 'frecuencia', 'fecha_de_registro'
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
