<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function getConfiguracion()
    {
        $configuracion = Configuracion::first();
        return response()->json($configuracion, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'IVA' => 'required|numeric|min:0|max:100',
            'porcentaje_ganancia' => 'required|numeric|min:0|max:100',
            'nombre_empresa' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'rif' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'direcciones' => 'nullable|array',
            'pago_movil' => 'nullable|array',
            'transferencias' => 'nullable|array',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // Validación de la imagen
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $logoPath = "images/logo/";
            $logoFilename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path($logoPath), $logoFilename);
            $validatedData['logo'] = $logoPath . $logoFilename;
        }

        $validatedData['numero_sucursales'] = 2;
        $configuracion = Configuracion::create($validatedData);
        return response()->json($configuracion, 201);
    }

    public function update(Request $request, Configuracion $configuracion)
    {
        $validatedData = $request->validate([
            'IVA' => 'sometimes|required|numeric|min:0|max:100',
            'porcentaje_ganancia' => 'sometimes|required|numeric|min:0|max:100',
            'nombre_empresa' => 'sometimes|nullable|string|max:255',
            'telefono' => 'sometimes|nullable|string|max:20',
            'rif' => 'sometimes|nullable|string|max:20',
            'correo' => 'sometimes|nullable|email|max:255',
            'direcciones' => 'sometimes|nullable|array',
            'pago_movil' => 'sometimes|nullable|array',
            'transferencias' => 'sometimes|nullable|array',
            'logo' => 'sometimes|nullable|file|mimes:jpg,jpeg,png|max:2048', // Validación de la imagen
        ]);

        if ($request->hasFile('logo')) {
            // Eliminar el logo antiguo si existe
            if ($configuracion->logo) {
                $oldLogoPath = public_path($configuracion->logo);
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }

            $file = $request->file('logo');
            $logoPath = "images/logo/";
            $logoFilename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path($logoPath), $logoFilename);
            $validatedData['logo'] = $logoPath . $logoFilename;
        }

        $configuracion->update($validatedData);
        return response()->json($configuracion, 200);
    }
}
