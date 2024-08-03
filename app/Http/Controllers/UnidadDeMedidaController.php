<?php

namespace App\Http\Controllers;

use App\Models\UnidadDeMedida;
use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;

class UnidadDeMedidaController extends Controller
{
    public function index()
    {
        return response()->json(UnidadDeMedida::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'abreviacion' => 'nullable|string|max:10',
        ]);

        $unidad = UnidadDeMedida::create($data);

        return response()->json($unidad, 201);
    }

    public function show($id)
    {
        $unidad = UnidadDeMedida::findOrFail($id);
        return response()->json($unidad);
    }

    public function update(Request $request,  $unidad)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'abreviacion' => 'nullable|string|max:10',
        ]);

        $unidad = UnidadDeMedida::findOrFail($unidad);
        $oldName = $unidad->nombre;
        $newName = $request->nombre;

        // Log de prueba
        Log::info('Actualizando Unidad de Medida:', ['oldName' => $oldName, 'newName' => $newName]);

        $unidad->nombre = $newName;
        $unidad->save();

        Producto::where('unidad_de_medida', $oldName)->update(['unidad_de_medida' => $newName]);

        return response()->json($unidad, 200);
    }

    public function destroy($unidad)
    {
        $unidad = UnidadDeMedida::findOrFail($unidad);
        $unidad->delete();
        return response()->json(null, 204);
    }
}
