<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'codigo_barras',
        'nombre',
        'descripcion',
        'categoria',
        'cantidad_en_stock',
        'unidad_de_medida',
        'ubicacion',
        'precio_compra',
        'porcentaje_ganancia',
        'descuento',
        'forma_de_venta',
        'proveedor',
        'fecha_entrada',
        'fecha_caducidad',
        'peso',
        'imagen',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // Add any attributes you want to hide by default
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public static function findByCodigoBarrasAndUbicacion($codigo_barras, $ubicacion)
    {
        return self::where('codigo_barras', $codigo_barras)
            ->where('ubicacion', $ubicacion)
            ->first();
    }
}
