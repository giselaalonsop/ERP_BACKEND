<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Configuracion extends Model
{
    use HasFactory;

    protected $fillable = [
        'IVA', 'porcentaje_ganancia', 'nombre_empresa', 'telefono', 'rif', 'correo', 'numero_sucursales', 
        'direcciones', 'pago_movil', 'transferencias', 'logo','habilitar',
    ];

    protected $casts = [
        'direcciones' => 'array', // Para manejar el campo JSON como array en Eloquent
        'pago_movil' => 'array', // Para manejar el campo JSON como array en Eloquent
        'transferencias' => 'array', // Para manejar el campo JSON como array en Eloquent
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
}
