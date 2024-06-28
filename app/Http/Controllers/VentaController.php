<?php

// app/Http/Controllers/VentaController.php

// app/Http/Controllers/VentaController.php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use App\Models\Cliente;

class VentaController extends Controller
{
    public function index()
    {
        return Venta::with('detalles')->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cliente' => 'required|string|max:255',
            'usuario' => 'required|string|max:255',
            'numero_de_venta' => 'required|integer',
            'total_venta' => 'required|numeric',
            'comprobante' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'mayor_o_detal' => 'required|string|max:255',
            'productos' => 'required|array',
            'productos.*.codigo_barras' => 'required|string|max:255',
            'productos.*.cantidad' => 'required|integer',
        ]);

        $venta = Venta::create([
            'cliente' => $validatedData['cliente'],
            'usuario' => $validatedData['usuario'],
            'fecha' => now(), // Ajustado para incluir la fecha y hora actual
            'numero_de_venta' => $validatedData['numero_de_venta'],
            'total_venta' => $validatedData['total_venta'],
            'comprobante' => $validatedData['comprobante'],
            'estado' => $validatedData['estado'],
            'mayor_o_detal' => $validatedData['mayor_o_detal'],
        ]);

        foreach ($validatedData['productos'] as $productoData) {
            $producto = Producto::where('codigo_barras', $productoData['codigo_barras'])->first();
            if ($producto) {
                $producto->cantidad_en_stock -= $productoData['cantidad'];
                $producto->save();

                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'codigo_barras' => $producto->codigo_barras,
                    'nombre' => $producto->nombre,
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $producto->precio_compra * (1 + $producto->porcentaje_ganancia / 100),
                    'total' => $productoData['cantidad'] * $producto->precio_compra * (1 + $producto->porcentaje_ganancia / 100)
                ]);
            }
        }
        $cliente = Cliente::where('cedula', $validatedData['cliente'])->first();
        if ($cliente) {
            $cliente->increment('numero_de_compras');
            $cliente->save();
        }



        return response()->json($venta->load('detalles'), 201);
    }

    public function show(Venta $venta)
    {
        return response()->json($venta->load('detalles'));
    }

    public function update(Request $request, Venta $venta)
    {
        // Aquí puedes agregar la lógica para actualizar una venta
    }

    public function destroy(Venta $venta)
    {
        $venta->delete();
        return response()->json(null, 204);
    }
}
