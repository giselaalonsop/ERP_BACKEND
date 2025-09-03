<?php

namespace App\Http\Controllers;

use App\Models\NumeroDeCuenta;
use Illuminate\Http\Request;

class NumeroDeCuentaController extends Controller
{
    public function index()
    {
        return NumeroDeCuenta::with('proveedor')->get();
    }

    public function show(NumeroDeCuenta $numeroDeCuenta)
    {
        return $numeroDeCuenta->load('proveedor');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'proveedor_id' => 'required',
            'banco' => 'required|string|max:255',
            'numero_cuenta' => 'required|string|max:50',
            'rif_cedula' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'pago_movil' => 'required|boolean',
        ]);

        $numeroDeCuenta = NumeroDeCuenta::create($validatedData);
        return response()->json($numeroDeCuenta, 201);
    }

    public function update(Request $request, NumeroDeCuenta $numeroDeCuenta)
    {
        $validatedData = $request->validate([
            'proveedor_id' => 'required',
            'banco' => 'required|string|max:255',
            'numero_cuenta' => 'required|string|max:50',
            'rif_cedula' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'pago_movil' => 'required|boolean',
        ]);

        $numeroDeCuenta->update($validatedData);
        return response()->json($numeroDeCuenta, 200);
    }

    public function destroy(NumeroDeCuenta $numeroDeCuenta)
    {
        $numeroDeCuenta->delete();
        return response()->json(null, 204);
    }
}
