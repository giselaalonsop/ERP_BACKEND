<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id', 'usuario_id', 'fecha', 'monto_total', 'monto_abonado', 'monto_restante', 'estado','habilitar'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
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
