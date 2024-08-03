<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class NumeroDeCuenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id', 'banco', 'numero_cuenta', 'rif_cedula', 'telefono', 'pago_movil','habilitar',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
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
