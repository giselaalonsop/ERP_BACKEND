<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Producto::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'codigo_barras' => 'required|max:255',
            'nombre' => 'required|max:255',
            'descripcion' => 'nullable',
            'categoria' => 'required|max:255',
            'cantidad_en_stock' => 'required|integer',
            'unidad_de_medida' => 'required|max:50',
            'ubicacion' => 'nullable|max:255',
            'precio_compra' => 'required|numeric',
            'porcentaje_ganancia' => 'required|numeric',
            'forma_de_venta' => 'required|max:255',
            'proveedor' => 'required|max:255',
            'fecha_entrada' => 'required|date',
            'fecha_caducidad' => 'nullable|date',
            'peso' => 'nullable|numeric',
            'imagen' => 'nullable|string|max:255',
        ]);

        $producto = Producto::create($validatedData);

        return response()->json($producto, 201);
    }

    public function cargarInventario(Request $request)
    {
        $validatedData = $request->validate([
            'codigo_barras' => 'required|string|max:255',
            'cantidad_a_cargar' => 'required|integer',
            'ubicacion_destino' => 'required|string|max:255'
        ]);

        $producto = Producto::where('codigo_barras', $validatedData['codigo_barras'])
            ->where('ubicacion', $validatedData['ubicacion_destino'])
            ->first();

        if ($producto) {
            // Producto existe en la ubicación especificada
            $producto->cantidad_en_stock += $validatedData['cantidad_a_cargar'];
            $producto->save();
        } else {
            // Crear un nuevo producto en la ubicación especificada
            $nuevoProducto = Producto::create([
                'codigo_barras' => $validatedData['codigo_barras'],
                'nombre' => $request->input('nombre'), // Asegúrate de pasar el nombre en la solicitud
                'descripcion' => $request->input('descripcion'),
                'categoria' => $request->input('categoria'),
                'cantidad_en_stock' => $validatedData['cantidad_a_cargar'],
                'unidad_de_medida' => $request->input('unidad_de_medida'),
                'ubicacion' => $validatedData['ubicacion_destino'],
                'precio_compra' => $request->input('precio_compra'),
                'porcentaje_ganancia' => $request->input('porcentaje_ganancia'),
                'forma_de_venta' => $request->input('forma_de_venta'),
                'proveedor' => $request->input('proveedor'),
                'fecha_entrada' => $request->input('fecha_entrada'),
                'fecha_caducidad' => $request->input('fecha_caducidad'),
                'peso' => $request->input('peso'),
                'imagen' => $request->input('imagen'),
            ]);

            return response()->json($nuevoProducto, 201);
        }

        return response()->json($producto, 200);
    }

    public function descargarInventario(Request $request)
    {
        $validatedData = $request->validate([
            'codigo_barras' => 'required|string|max:255',
            'cantidad_a_descargar' => 'required|integer',
            'ubicacion_origen' => 'required|string|max:255',
            'ubicacion_destino' => 'nullable|string|max:255',
            'cantidad_a_enviar' => 'nullable|integer',
        ]);

        // Encontrar el producto en la ubicación de origen
        $producto = Producto::where('codigo_barras', $validatedData['codigo_barras'])
            ->where('ubicacion', $validatedData['ubicacion_origen'])
            ->first();

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado en la ubicación de origen'], 404);
        }

        if ($validatedData['cantidad_a_descargar'] > $producto->cantidad_en_stock) {
            return response()->json(['error' => 'Cantidad a descargar supera la cantidad en stock'], 422);
        }

        // Restar la cantidad en la ubicación de origen
        $producto->cantidad_en_stock -= $validatedData['cantidad_a_descargar'];
        $producto->save();

        // Si se especifica una ubicación de destino y una cantidad a enviar
        if (isset($validatedData['ubicacion_destino']) && isset($validatedData['cantidad_a_enviar'])) {
            // Verificar si ya existe el producto en la ubicación de destino
            $destinoProducto = Producto::where('codigo_barras', $validatedData['codigo_barras'])
                ->where('ubicacion', $validatedData['ubicacion_destino'])
                ->first();

            if ($destinoProducto) {
                // Sumar la cantidad en la ubicación de destino
                $destinoProducto->cantidad_en_stock += $validatedData['cantidad_a_enviar'];
                $destinoProducto->save();
            } else {
                // Crear un nuevo producto en la ubicación de destino
                $nuevoProducto = Producto::create([
                    'codigo_barras' => $validatedData['codigo_barras'],
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'categoria' => $producto->categoria,
                    'cantidad_en_stock' => $validatedData['cantidad_a_enviar'],
                    'unidad_de_medida' => $producto->unidad_de_medida,
                    'ubicacion' => $validatedData['ubicacion_destino'],
                    'precio_compra' => $producto->precio_compra,
                    'porcentaje_ganancia' => $producto->porcentaje_ganancia,
                    'forma_de_venta' => $producto->forma_de_venta,
                    'proveedor' => $producto->proveedor,
                    'fecha_entrada' => $producto->fecha_entrada,
                    'fecha_caducidad' => $producto->fecha_caducidad,
                    'peso' => $producto->peso,
                    'imagen' => $producto->imagen,
                ]);

                return response()->json($nuevoProducto, 201);
            }
        }

        return response()->json($producto, 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        //
    }
}
