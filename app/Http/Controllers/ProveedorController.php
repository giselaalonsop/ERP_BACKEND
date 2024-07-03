<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    public function index()
    {
        return Proveedor::all();
    }

    public function show(Proveedor $proveedor)
    {
        return $proveedor;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'empresa' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
        ]);

        $proveedor = Proveedor::create($validatedData);
        return response()->json($proveedor, 201);
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'empresa' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
        ]);

        $proveedor->update($validatedData);
        return response()->json($proveedor, 200);
    }

    public function delete(Proveedor $proveedor)
    {
        $proveedor->delete();
        return response()->json(null, 204);
    }
}
