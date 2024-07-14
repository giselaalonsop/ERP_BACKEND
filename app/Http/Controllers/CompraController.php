<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class CompraController extends Controller
{
    public function index()
    {
        return Compra::with(['proveedor'])->get();
    }

    public function show(Compra $compra)
    {
        return $compra->load(['proveedor']);
    }

    public function store(Request $request)
    {
        try {
            // Log the raw input data
            Log::info('Datos recibidos para la compra: ', $request->all());

            // Validate the request data
            $validatedData = $request->validate([
                'proveedor_id' => 'required|exists:proveedors,id',
                'usuario_id' => 'required|exists:users,id',
                'fecha' => 'required|date',
                'monto_total' => 'required|numeric',
                'monto_abonado' => 'required|numeric',
                'estado' => ['required', Rule::in(['pendiente', 'pagada'])],
            ]);
            //calcular monto restante
            $validatedData['monto_restante'] = $validatedData['monto_total'] - $validatedData['monto_abonado'];
            // Create the compra record
            $compra = Compra::create($validatedData);

            // Log the created compra
            Log::info('Compra registrada: ', $compra->toArray());

            return response()->json($compra, 201);
        } catch (\Exception $e) {
            Log::error('Error al registrar la compra: ' . $e->getMessage());
            return response()->json(['error' => 'Hubo un problema al procesar la solicitud'], 500);
        }
    }

    public function update(Request $request, Compra $compra)
    {
        $validatedData = $request->validate([
            'proveedor_id' => 'required|exists:proveedors,id',
            'usuario_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'monto_total' => 'required',
            'monto_abonado' => 'required',
            'estado' => ['required', Rule::in(['pendiente', 'pagada'])],
        ]);

        $validatedData['monto_restante'] = $validatedData['monto_total'] - $validatedData['monto_abonado'];

        $compra->update($validatedData);
        return response()->json($compra, 200);
    }

    public function destroy(Compra $compra)
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
