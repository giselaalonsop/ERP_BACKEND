<?php

namespace App\Http\Controllers;

use App\Models\FormaDeVenta;
use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;

class FormaDeVentaController extends Controller
{
    public function index()
    {
        return response()->json(FormaDeVenta::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $forma = FormaDeVenta::create($data);

        return response()->json($forma, 201);
    }

    public function show($id)
    {
        $forma = FormaDeVenta::findOrFail($id);
        return response()->json($forma);
    }

    public function update(Request $request,  $forma)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $oldName = $forma->nombre;
        $newName = $request->nombre;

        $forma = FormaDeVenta::findOrFail($forma);
        $forma->nombre = $newName;
        $forma->save();

        // Actualizar la forma de venta en los productos
        Producto::where('forma_de_venta', $oldName)->update(['forma_de_venta' => $newName]);

        // Actualizar la forma de venta mayor en los productos
        Producto::where('forma_de_venta_mayor', $oldName)->update(['forma_de_venta_mayor' => $newName]);

        return response()->json($forma, 200);
    }


    public function destroy($forma)
    {
        $forma = FormaDeVenta::findOrFail($forma);
        Log::info($forma);

        $forma->delete();

        return response()->json(null, 204);
    }
}
