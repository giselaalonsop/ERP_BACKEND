<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompraController extends Controller
{
    public function index()
    {
        return Compra::with(['proveedor', 'usuario'])->get();
    }

    public function show(Compra $compra)
    {
        return $compra->load(['proveedor', 'usuario']);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'usuario_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'monto_total' => 'required|numeric|min:0',
            'monto_abonado' => 'required|numeric|min:0|max:monto_total',
            'estado' => ['required', Rule::in(['pendiente', 'pagada'])],
        ]);

        $validatedData['monto_restante'] = $validatedData['monto_total'] - $validatedData['monto_abonado'];

        $compra = Compra::create($validatedData);
        return response()->json($compra, 201);
    }

    public function update(Request $request, Compra $compra)
    {
        $validatedData = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'usuario_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'monto_total' => 'required|numeric|min:0',
            'monto_abonado' => 'required|numeric|min:0|max:monto_total',
            'estado' => ['required', Rule::in(['pendiente', 'pagada'])],
        ]);

        $validatedData['monto_restante'] = $validatedData['monto_total'] - $validatedData['monto_abonado'];

        $compra->update($validatedData);
        return response()->json($compra, 200);
    }

    public function delete(Compra $compra)
    {
        $compra->delete();
        return response()->json(null, 204);
    }

    public function abonar(Request $request, Compra $compra)
    {
        $validatedData = $request->validate([
            'monto' => 'required|numeric|min:0|max:' . $compra->monto_restante,
        ]);

        $compra->abonar($validatedData['monto']);
        return response()->json($compra, 200);
    }
}
