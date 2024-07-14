<?php

namespace App\Http\Controllers;

use App\Models\CierreDeCaja;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CierreDeCajaController extends Controller
{

    public function index()
    {
        return CierreDeCaja::with('usuario')->get();
    }

    // app/Http/Controllers/CierreDeCajaController.php

    public function show($ubicacion)
    {
        $today = Carbon::now()->toDateString();
        $cierreDeCaja = CierreDeCaja::where('fecha', $today)
            ->where('ubicacion', $ubicacion)
            ->first();

        if (!$cierreDeCaja) {
            return response()->json(['error' => 'No hay caja abierta ni cerrada para hoy en esta ubicación.'], 400);
        }

        if ($cierreDeCaja->estado === 'cerrado') {
            return response()->json(['message' => 'La caja del día ya fue cerrada.'], 200);
        }

        return response()->json($cierreDeCaja);
    }

    public function registrarVenta(Request $request)
    {
        $validatedData = $request->validate([
            'location' => 'required|string|max:255',
            'total_venta_dol' => 'required|numeric',
            'payments' => 'required|array',
            'payments.*.method' => 'required|string',
            'payments.*.amount' => 'required|numeric',
            'payments.*.change' => 'required|numeric',
        ]);

        $today = Carbon::now()->toDateString();
        $ubicacion = $validatedData['location'];

        $existingCierre = CierreDeCaja::where('fecha', $today)
            ->where('ubicacion', $ubicacion)
            ->first();

        if ($existingCierre && $existingCierre->estado === 'cerrado') {
            return response()->json(['error' => 'La caja está cerrada para hoy en esta ubicación.'], 400);
        }

        $montoTotal = $validatedData['total_venta_dol'];
        $dolEfectivo = 0;
        $zelle = 0;
        $bsEfectivo = 0;
        $bsPuntoDeVenta = 0;
        $bsPagoMovil = 0;

        foreach ($validatedData['payments'] as $payment) {
            switch ($payment['method']) {
                case 'dol_efectivo':
                    $dolEfectivo += $payment['amount'] - $payment['change'];
                    break;
                case 'zelle':
                    $zelle += $payment['amount'] - $payment['change'];
                    break;
                case 'bs_efectivo':
                    $bsEfectivo += ($payment['amount'] - $payment['change']);
                    break;
                case 'bs_punto_de_venta':
                    $bsPuntoDeVenta += ($payment['amount'] - $payment['change']);
                    break;
                case 'bs_pago_movil':
                    $bsPagoMovil += ($payment['amount'] - $payment['change']);
                    break;
            }
        }

        if ($existingCierre) {
            $existingCierre->increment('monto_total', $montoTotal);
            $existingCierre->increment('dol_efectivo', $dolEfectivo);
            $existingCierre->increment('zelle', $zelle);
            $existingCierre->increment('bs_efectivo', $bsEfectivo);
            $existingCierre->increment('bs_punto_de_venta', $bsPuntoDeVenta);
            $existingCierre->increment('bs_pago_movil', $bsPagoMovil);
        } else {
            CierreDeCaja::create([
                'usuario_id' => auth()->id(),
                'monto_total' => $montoTotal,
                'dol_efectivo' => $dolEfectivo,
                'zelle' => $zelle,
                'bs_efectivo' => $bsEfectivo,
                'bs_punto_de_venta' => $bsPuntoDeVenta,
                'bs_pago_movil' => $bsPagoMovil,
                'fecha' => $today,
                'estado' => 'abierto',
                'ubicacion' => $ubicacion,
            ]);
        }

        return response()->json(['message' => 'Venta registrada en caja.'], 200);
    }
    public function cerrarCaja(Request $request)
    {
        $ubicacion = $request->input('ubicacion');
        $today = Carbon::now()->toDateString();
        $cierreDeCaja = CierreDeCaja::where('fecha', $today)
            ->where('ubicacion', $ubicacion)
            ->where('estado', 'abierto')
            ->first();

        if (!$cierreDeCaja) {
            return response()->json(['error' => 'No hay caja abierta para hoy en esta ubicación.'], 400);
        }

        $cierreDeCaja->update([
            'estado' => 'cerrado',
        ]);

        return response()->json(['message' => 'Caja cerrada exitosamente.'], 200);
    }
}
