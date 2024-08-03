<?php

// app/Http/Controllers/ClienteController.php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Models\Venta;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;


class ClienteController extends Controller
{
    public function index()
    {
        return Cliente::where('habilitar', 1)->get();
    }
    public function inHabilitados()
    {
        return Cliente::where('habilitar', 0)->get();
    }
    public function habilitar($id)
    {
        $Cliente = Cliente::find($id);
        $Cliente->habilitar = 1;
        $Cliente->save();
        return response()->json($Cliente, 200);
    }


    public function historialCompras($cedula)
    {
        $cliente = Cliente::where('cedula', $cedula)->firstOrFail();
        $compras = Venta::where('cliente', $cedula)->with('detalles')->get();

        return response()->json($compras);
    }
    // app/Http/Controllers/ClienteController.php

    public function ultimaCompra($cedula)
    {
        $cliente = Cliente::where('cedula', $cedula)->firstOrFail();
        $compra = Venta::where('cliente', $cedula)->with('detalles')->latest()->first();

        if ($compra) {
            return response()->json([
                'fecha' => $compra->created_at->format('Y-m-d'),

            ]);
        } else {
            return response()->json(['fecha' => 'N/A']);
        }
    }



    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'correo_electronico' => 'required|string|email|max:255|unique:clientes,correo_electronico',
                'numero_de_telefono' => 'required|string|max:20',
                'direccion' => 'required|string|max:255',
                'cedula' => 'required|string|max:20|unique:clientes,cedula',
                'edad' => 'required|integer',
                'descuento' => 'required|numeric',
            ]);

            $cliente = Cliente::create(array_merge($validatedData, [
                'numero_de_compras' => 0,
                'cantidad_de_articulos_comprados' => 0,
                'estatus' => 'Activo',
                'frecuencia' => 0,
                'fecha_de_registro' => now(),
            ]));

            return response()->json($cliente, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (QueryException $e) {
            Log::error('Error al registrar cliente: ' . $e->getMessage());
            if ($e->errorInfo[1] == 1062) { // Código de error para entrada duplicada
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('Error al registrar cliente: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Cliente $cliente)
    {
        return response()->json($cliente);
    }

    public function update(Request $request, Cliente $cliente)
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'apellido' => 'sometimes|required|string|max:255',
                'correo_electronico' => 'sometimes|required|string|email|max:255|unique:clientes,correo_electronico,' . $cliente->id,
                'numero_de_telefono' => 'sometimes|required|string|max:20',
                'direccion' => 'sometimes|required|string|max:255',
                'cedula' => 'sometimes|required|string|max:20|unique:clientes,cedula,' . $cliente->id,
                'edad' => 'sometimes|required|integer',
                'descuento' => 'sometimes|required|numeric',
            ]);

            $cliente->update($validatedData);

            return response()->json($cliente);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (QueryException $e) {
            Log::error('Error al actualizar cliente: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('Error al actualizar cliente: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function destroy(Cliente $cliente)
    {
        $cliente->habilitar = 0;
        $cliente->save();
        return response()->json(null, 204);
    }
}
