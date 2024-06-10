<?php


namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        return Cliente::all();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'correo_electronico' => 'required|string|email|max:255', // actualizado
            'numero_de_telefono' => 'required|string|max:20', // actualizado
            'direccion' => 'required|string|max:255',
            'cedula' => 'required|string|max:20|unique:clientes,cedula',
            'edad' => 'required|integer',
        ]);

        $cliente = Cliente::create(array_merge($validatedData, [
            'numero_de_compras' => 0, // actualizado
            'cantidad_de_articulos_comprados' => 0, // actualizado
            'estatus' => 'Activo',
            'frecuencia' => 0, // actualizado
            'fecha_de_registro' => now(),
        ]));

        return response()->json($cliente, 201);
    }
    public function show(Cliente $cliente)
    {
        return response()->json($cliente);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validatedData = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255',
            'correo_electronico' => 'sometimes|required|string|email|max:255',
            'numero_de_telefono' => 'sometimes|required|string|max:20',
            'direccion' => 'sometimes|required|string|max:255',
            'cedula' => 'sometimes|required|string|max:20|unique:clientes,cedula,' . $cliente->id,
            'edad' => 'sometimes|required|integer',
        ]);

        $cliente->update($validatedData);

        return response()->json($cliente);
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return response()->json(null, 204);
    }
}
