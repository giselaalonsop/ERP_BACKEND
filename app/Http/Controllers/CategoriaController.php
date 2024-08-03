<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Models\Producto;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorias = Categoria::where('habilitar', 1)->get();

        return response()->json($categorias);
    }
    public function inHabilitados()
    {
        return Categoria::where('habilitar', 0)->get();
    }
    public function habilitar($id)
    {
        $categoria = Categoria::find($id);
        $categoria->habilitar = 1;
        $categoria->save();
        return response()->json($categoria, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $categoria = Categoria::create([
            'nombre' => $request->nombre,
        ]);

        return response()->json($categoria, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $oldName = $categoria->nombre;
        $newName = $request->nombre;

        // Actualizar el nombre de la categoría
        $categoria->nombre = $newName;
        $categoria->save();

        // Actualizar los productos que tienen esta categoría
        Producto::where('categoria', $oldName)->update(['categoria' => $newName]);

        return response()->json($categoria, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        $categoria->habilitar = 0;
        $categoria->save();
    }
}
