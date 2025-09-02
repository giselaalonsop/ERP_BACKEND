<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class Producto extends Model
{
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
        'fecha_entrada',
        'fecha_caducidad',
        'peso',
        'imagen',
        'porcentaje_ganancia_mayor',
        'forma_de_venta_mayor',
        'cantidad_por_caja',
        'cantidad_en_stock_mayor',
        'habilitar',
    ];
    protected static function booted()
    {
        static::created(function ($model) {
            self::logChanges($model, 'created');
        });

        static::updated(function ($model) {
            self::logChanges($model, 'updated');
        });

        static::deleted(function ($model) {
            self::logChanges($model, 'deleted');
        });
    }

    protected static function logChanges($model, $action)
    {
        $user = Auth::user();
        $oldValues = $action === 'updated' || $action === 'deleted' ? json_encode($model->getOriginal()) : null;
        $newValues = $action !== 'deleted' ? json_encode($model->getAttributes()) : null;

        AuditLog::create([
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'table_name' => $model->getTable(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    public function getPrecioVentaMayorAttribute()
    {
        return $this->precio_compra * (1 + $this->porcentaje_ganancia_mayor / 100);
    }

    public function getPrecioVentaDetalAttribute()
    {
        return $this->precio_compra * (1 + $this->porcentaje_ganancia_detal / 100);
    }
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
