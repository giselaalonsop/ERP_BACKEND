<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ConfiguracionController extends Controller
{
    public function getConfiguracion()
    {
        $configuracion = Configuracion::first();
        return response()->json($configuracion, 200);
    }

    public function store(Request $request)

    {
        try {

            $configuraciones = Configuracion::first();


            if ($configuraciones) {


                if ($request->hasFile('logo')) {
                    $file = $request->file('logo');
                    $logoPath = "images/logo/";
                    $logoFilename = time() . '-' . $file->getClientOriginalName();
                    $file->move(public_path($logoPath), $logoFilename);
                    $validatedData['logo'] = $logoPath . $logoFilename;
                } else {
                    $validatedData['logo'] = $configuraciones->logo;
                }
            }

            $validatedData['numero_sucursales'] = 2;

            if ($configuraciones) {
                $configuraciones->update([
                    'IVA' => $request->IVA,
                    'porcentaje_ganancia' => $request->porcentaje_ganancia,
                    'nombre_empresa' => $request->nombre_empresa,
                    'telefono' => $request->telefono,
                    'rif' => $request->rif,
                    'correo' => $request->correo,
                    'numero_sucursales' => $validatedData['numero_sucursales'],
                    'direcciones' => $request->direcciones,
                    'pago_movil' => $request->pago_movil,
                    'transferencias' => $request->transferencias,
                    'logo' => $validatedData['logo'],
                ]);
            } else {
                $configuraciones->Configuracion::create([
                    'IVA' => $request->IVA,
                    'porcentaje_ganancia' => $request->porcentaje_ganancia,
                    'nombre_empresa' => $request->nombre_empresa,
                    'telefono' => $request->telefono,
                    'rif' => $request->rif,
                    'correo' => $request->correo,
                    'numero_sucursales' => $validatedData['numero_sucursales'],
                    'direcciones' => $request->direcciones,
                    'pago_movil' => $request->pago_movil,
                    'transferencias' => $request->transferencias,
                    'logo' => $validatedData['logo'],
                ]);
            }
            return response($configuraciones, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
