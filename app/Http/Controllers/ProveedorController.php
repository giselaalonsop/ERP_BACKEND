<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use App\Models\NumeroDeCuenta;
use Illuminate\Support\Facades\Log;

class ProveedorController extends Controller
{
    public function index()
    {
        return Proveedor::with('numerosDeCuenta')->get();
    }

    public function show(Proveedor $proveedor)
    {
        return $proveedor->load('numerosDeCuenta');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'empresa' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'numeros_de_cuenta' => 'required|array',
            'numeros_de_cuenta.*.banco' => 'required|string|max:255',
            'numeros_de_cuenta.*.numero_cuenta' => 'required|string|max:50',
            'numeros_de_cuenta.*.rif_cedula' => 'required|string|max:20',
            'numeros_de_cuenta.*.telefono' => 'nullable|string|max:20',
            'numeros_de_cuenta.*.pago_movil' => 'required|boolean',
        ]);

        $proveedor = Proveedor::create($validatedData);

        foreach ($validatedData['numeros_de_cuenta'] as $cuenta) {
            $proveedor->numerosDeCuenta()->create($cuenta);
        }

        return response()->json($proveedor->load('numerosDeCuenta'), 201);
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'empresa' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'numeros_de_cuenta' => 'required|array',
            'numeros_de_cuenta.*.id' => 'nullable|exists:numero_de_cuentas,id',
            'numeros_de_cuenta.*.banco' => 'required|string|max:255',
            'numeros_de_cuenta.*.numero_cuenta' => 'required|string|max:50',
            'numeros_de_cuenta.*.rif_cedula' => 'required|string|max:20',
            'numeros_de_cuenta.*.telefono' => 'nullable|string|max:20',
            'numeros_de_cuenta.*.pago_movil' => 'required|boolean',
        ]);

        $proveedor->update($validatedData);

        foreach ($validatedData['numeros_de_cuenta'] as $cuenta) {
            if (isset($cuenta['id'])) {
                $numeroDeCuenta = NumeroDeCuenta::find($cuenta['id']);
                $numeroDeCuenta->update($cuenta);
            } else {
                $proveedor->numerosDeCuenta()->create($cuenta);
            }
        }

        return response()->json($proveedor->load('numerosDeCuenta'), 200);
    }

    public function destroy(Proveedor $proveedor)
    {
        try {
            $proveedor->delete();
            Log::info('Proveedor eliminado correctamente', ['proveedor_id' => $proveedor->id]);
            return response()->json(['message' => 'Proveedor eliminado correctamente'], 204);
        } catch (\Exception $e) {
            Log::error('Error al eliminar el proveedor', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error al eliminar el proveedor', 'error' => $e->getMessage()], 500);
        }
    }
}
