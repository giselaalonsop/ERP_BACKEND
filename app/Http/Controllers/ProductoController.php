<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index()
    {
        return Producto::where('habilitar', 1)->get();
    }
    public function inhabilitados()
    {
        return Producto::where('habilitar', 0)->get();
    }
    public function habilitar($id)
    {
        $producto = Producto::find($id);
        $producto->habilitar = 1;
        $producto->save();
        return response()->json($producto, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'codigo_barras' => 'required|max:255|unique:productos',
            'nombre' => 'required|max:255',
            'descripcion' => 'nullable',
            'categoria' => 'required|max:255',
            'cantidad_en_stock' => 'required|numeric',
            'cantidad_en_stock_mayor' => 'required|integer',
            'unidad_de_medida' => 'required|max:50',
            'ubicacion' => 'nullable|max:255',
            'precio_compra' => 'required|numeric',
            'porcentaje_ganancia' => 'required|numeric',
            'porcentaje_ganancia_mayor' => 'required|numeric',
            'forma_de_venta' => 'required|max:255',
            'forma_de_venta_mayor' => 'required|max:255',
            'proveedor' => 'required|max:255',
            'fecha_entrada' => 'required|date',
            'fecha_caducidad' => 'nullable|date',
            'peso' => 'nullable|numeric',

            'cantidad_por_caja' => 'required|integer',
        ]);

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $imagenPath = "images/productos/";
            $imagenFilename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path($imagenPath), $imagenFilename);
            $validatedData['imagen'] = $imagenPath . $imagenFilename;
        } elseif (is_string($request->input('imagen'))) {
            // Si la imagen es una cadena de texto, significa que ya existe y no se ha actualizado
            unset($validatedData['imagen']); // No actualizar el campo
        }

        try {
            $producto = Producto::create($validatedData);
            return response()->json($producto, 201);
        } catch (\Exception $e) {
            Log::error('Error al registrar producto: ' . $e->getMessage());
            return response()->json(['error' => 'Error al registrar el producto'], 500);
        }
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
            $cantidad_a_cargar_mayor = floor($validatedData['cantidad_a_cargar'] / $producto->cantidad_por_caja);
            $producto->cantidad_en_stock += $validatedData['cantidad_a_cargar'];
            $producto->cantidad_en_stock_mayor += $cantidad_a_cargar_mayor;
            $producto->save();
        } else {
            $cantidad_a_cargar_mayor = floor($validatedData['cantidad_a_cargar'] / $request->input('cantidad_por_caja'));
            $nuevoProducto = Producto::create([
                'codigo_barras' => $validatedData['codigo_barras'],
                'nombre' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion'),
                'categoria' => $request->input('categoria'),
                'cantidad_en_stock' => $validatedData['cantidad_a_cargar'],
                'cantidad_en_stock_mayor' => $cantidad_a_cargar_mayor,
                'unidad_de_medida' => $request->input('unidad_de_medida'),
                'ubicacion' => $validatedData['ubicacion_destino'],
                'precio_compra' => $request->input('precio_compra'),
                'porcentaje_ganancia' => $request->input('porcentaje_ganancia'),
                'porcentaje_ganancia_mayor' => $request->input('porcentaje_ganancia_mayor'),
                'forma_de_venta_mayor' => $request->input('forma_de_venta_mayor'),
                'forma_de_venta' => $request->input('forma_de_venta'),
                'proveedor' => $request->input('proveedor'),
                'fecha_entrada' => $request->input('fecha_entrada'),
                'fecha_caducidad' => $request->input('fecha_caducidad'),
                'peso' => $request->input('peso'),
                'imagen' => $request->input('imagen'),
                'cantidad_por_caja' => $request->input('cantidad_por_caja'),
            ]);

            return response()->json($nuevoProducto, 201);
        }

        return response()->json($producto, 200);
    }

    public function descargarInventario(Request $request)
    {
        Log::info('Datos recibidos en la solicitud', ['data' => $request->all()]);
        $validatedData = $request->validate([
            'codigo_barras' => 'required|string|max:255',
            'cantidad_a_descargar' => 'required|integer',
            'ubicacion_origen' => 'required|string|max:255',
            'ubicacion_destino' => 'nullable|string|max:255',
            'cantidad_a_enviar' => 'nullable|integer',
        ]);

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
        $cantidad_a_descargar_mayor = floor($validatedData['cantidad_a_descargar'] / $producto->cantidad_por_caja);
        $producto->cantidad_en_stock_mayor -= $cantidad_a_descargar_mayor;

        $producto->save();

        if (isset($validatedData['ubicacion_destino']) && isset($validatedData['cantidad_a_enviar'])) {
            $destinoProducto = Producto::where('codigo_barras', $validatedData['codigo_barras'])
                ->where('ubicacion', $validatedData['ubicacion_destino'])
                ->first();

            if ($destinoProducto) {
                $cantidad_a_enviar_mayor = floor($validatedData['cantidad_a_enviar'] / $producto->cantidad_por_caja);
                $destinoProducto->cantidad_en_stock += $validatedData['cantidad_a_enviar'];
                $destinoProducto->cantidad_en_stock_mayor += $cantidad_a_enviar_mayor;
                $destinoProducto->save();
            } else {
                $cantidad_a_enviar_mayor = floor($validatedData['cantidad_a_enviar'] / $producto->cantidad_por_caja);
                $nuevoProducto = Producto::create([
                    'codigo_barras' => $validatedData['codigo_barras'],
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'categoria' => $producto->categoria,
                    'cantidad_en_stock' => $validatedData['cantidad_a_enviar'],
                    'cantidad_en_stock_mayor' => $cantidad_a_enviar_mayor,
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
                    'porcentaje_ganancia_mayor' => $producto->porcentaje_ganancia_mayor,
                    'forma_de_venta_mayor' => $producto->forma_de_venta_mayor,
                    'cantidad_por_caja' => $producto->cantidad_por_caja,
                ]);

                return response()->json($nuevoProducto, 201);
            }
        }

        return response()->json($producto, 200);
    }



    public function show(Producto $producto)
    {
        //
    }

    public function edit(Producto $producto)
    {
        //
    }

    public function update(Request $request, Producto $producto)
    {
        // Log para verificar los datos recibidos en la solicitud
        Log::info('Datos recibidos en la solicitud', ['data' => $request->all()]);

        $validatedData = $request->validate([
            'codigo_barras' => [
                'sometimes',
                'required',
                'max:255',
                // Aquí se ignora el producto actual para evitar el error de unicidad
                Rule::unique('productos')->where(function ($query) use ($request) {
                    return $query->where('ubicacion', $request->ubicacion);
                })->ignore($producto->id)
            ],
            'nombre' => 'sometimes|required|max:255',
            'descripcion' => 'nullable',
            'categoria' => 'sometimes|required|max:255',
            'cantidad_en_stock' => 'sometimes|required|numeric',
            'cantidad_en_stock_mayor' => 'sometimes|required|integer',
            'unidad_de_medida' => 'sometimes|required|max:50',
            'ubicacion' => 'nullable|max:255',
            'precio_compra' => 'sometimes|required|numeric',
            'porcentaje_ganancia' => 'sometimes|required|numeric',
            'porcentaje_ganancia_mayor' => 'sometimes|required|numeric',
            'forma_de_venta' => 'sometimes|required|max:255',
            'forma_de_venta_mayor' => 'sometimes|required|max:255',
            'proveedor' => 'sometimes|required|max:255',
            'fecha_entrada' => 'sometimes|required|date',
            'fecha_caducidad' => 'nullable|date',
            'peso' => 'nullable|numeric',
            'imagen' => 'nullable',
            'cantidad_por_caja' => 'sometimes|required|integer',
        ]);
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $imagenPath = "images/productos/";
            $imagenFilename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path($imagenPath), $imagenFilename);
            $validatedData['imagen'] = $imagenPath . $imagenFilename;
        }

        $producto->update($validatedData);

        return response()->json($producto, 200);
    }


    public function destroy(Producto $producto)
    {
        $producto->habilitar = 0; // Deshabilitar el producto
        $producto->save(); // Guardar el cambio

        return response()->json(null, 204); // Retorna una respuesta exitosa sin contenido
    }
}
