<?php

// app/Http/Controllers/VentaDetalleController.php

namespace App\Http\Controllers;

use App\Models\VentaDetalle;
use Illuminate\Http\Request;

class VentaDetalleController extends Controller
{
    public function index()
    {
        return VentaDetalle::all();
    }

    public function show(VentaDetalle $ventaDetalle)
    {
        return response()->json($ventaDetalle);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'codigo_barras' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'cantidad' => 'required|integer',
            'precio_unitario' => 'required|numeric',
            'total' => 'required|numeric'
        ]);

        $ventaDetalle = VentaDetalle::create($validatedData);

        return response()->json($ventaDetalle, 201);
    }

    public function update(Request $request, VentaDetalle $ventaDetalle)
    {
        $validatedData = $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'codigo_barras' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'cantidad' => 'required|numeric',
            'precio_unitario' => 'required|numeric',
            'total' => 'required|numeric'
        ]);

        $ventaDetalle->update($validatedData);

        return response()->json($ventaDetalle);
    }

    public function destroy(VentaDetalle $ventaDetalle)
    {
        $ventaDetalle->delete();

        return response()->json(null, 204);
    }
}
