<?php

// app/Models/Venta.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
}
