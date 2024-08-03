<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\CierreDeCaja;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function index()
    {
        return Venta::with('detalles')->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cliente' => 'required|string|max:255',
            'usuario' => 'required|string|max:255',
            'comprobante' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'mayor_o_detal' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'total_venta_bs' => 'required|numeric',
            'total_venta_dol' => 'required|numeric',
            'metodo_pago' => 'required|array',
            'metodo_pago.*.method' => 'required|string|in:dol_efectivo,bs_punto_de_venta,bs_pago_movil,zelle,bs_efectivo,pagar_luego',
            'metodo_pago.*.amount' => 'required|numeric',
            'metodo_pago.*.change' => 'required|numeric',
            'productos' => 'required|array',
            'productos.*.id' => 'required|integer',
            'productos.*.cantidad' => 'required|numeric',
            'descuento' => 'required|numeric'
        ]);

        // Obtener el último número de venta
        $lastVenta = Venta::orderBy('numero_de_venta', 'desc')->first();
        $newNumeroDeVenta = $lastVenta ? $lastVenta->numero_de_venta + 1 : 1;

        $venta = Venta::create([
            'cliente' => $validatedData['cliente'],
            'usuario' => $validatedData['usuario'],
            'fecha' => now(),
            'numero_de_venta' => $newNumeroDeVenta,
            'comprobante' => $validatedData['comprobante'],
            'estado' => $validatedData['estado'],
            'mayor_o_detal' => $validatedData['mayor_o_detal'],
            'location' => $validatedData['location'],
            'total_venta_bs' => $validatedData['total_venta_bs'],
            'total_venta_dol' => $validatedData['total_venta_dol'],
            'metodo_pago' => json_encode($validatedData['metodo_pago']), // Almacenar como JSON
            'descuento' => $validatedData['descuento']
        ]);

        foreach ($validatedData['productos'] as $productoData) {
            $producto = Producto::find($productoData['id']);
            if ($producto) {
                $producto->cantidad_en_stock -= $productoData['cantidad'];
                $producto->save();

                $precioUnitario = $validatedData['mayor_o_detal'] === 'Mayor'
                    ? $producto->precio_compra * (1 + ($producto->porcentaje_ganancia_mayor - $validatedData['descuento']) / 100)
                    : $producto->precio_compra * (1 + ($producto->porcentaje_ganancia - $validatedData['descuento']) / 100);

                $totalProducto = $productoData['cantidad'] * $precioUnitario;

                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'codigo_barras' => $producto->codigo_barras,
                    'nombre' => $producto->nombre,
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $precioUnitario,
                    'total' => $totalProducto
                ]);
            }
        }

        $cliente = Cliente::where('cedula', $validatedData['cliente'])->first();
        if ($cliente) {
            $cliente->increment('numero_de_compras');
            $cliente->save();
        }

        return response()->json($venta->load('detalles'), 201);
    }

    public function show(Venta $venta)
    {
        return response()->json($venta->load('detalles'));
    }

    public function update(Request $request, Venta $venta)
    {
        $validatedData = $request->validate([
            'estado' => 'required|string|max:255',
            'metodo_pago' => 'required|array',
            'metodo_pago.*.method' => 'required|string|in:dol_efectivo,bs_punto_de_venta,bs_pago_movil,zelle,bs_efectivo,pagar_luego',
            'metodo_pago.*.amount' => 'required|numeric',
            'metodo_pago.*.change' => 'required|numeric'
        ]);

        // Actualizar el estado y los métodos de pago de la venta
        $venta->update([
            'estado' => $validatedData['estado'],
            'metodo_pago' => json_encode($validatedData['metodo_pago']) // Almacenar como JSON
        ]);

        return response()->json(['message' => 'Venta actualizada.', 'venta' => $venta->load('detalles')], 200);
    }

    public function ventasPendientes()
    {
        $ventasPendientes = Venta::where('estado', 'Pendiente')
            ->get();

        return response()->json($ventasPendientes->load('detalles'));
    }

    public function destroy(Venta $venta)
    {
        $today = Carbon::today()->startOfDay();
        $ubicacion = $venta->location;
        if ($venta->estado !== 'Pendiente') {
            $cierreDeCaja = CierreDeCaja::where('ubicacion', $ubicacion)
                ->where('created_at', '>=', $today)
                ->first();
        }

        foreach ($venta->detalles as $detalle) {
            $producto = Producto::where('codigo_barras', $detalle->codigo_barras)
                ->where('ubicacion', $ubicacion)
                ->first();

            if ($producto) {
                $producto->cantidad_en_stock += $detalle->cantidad;
                $producto->save();
            }
        }

        if ($cierreDeCaja && $cierreDeCaja->estado === 'abierto' && $venta->created_at->startOfDay()->eq($today)) {
            $metodoPago = json_decode($venta->metodo_pago, true);

            $montoTotal = $venta->total_venta_dol;
            $dolEfectivo = 0;
            $zelle = 0;
            $bsEfectivo = 0;
            $bsPuntoDeVenta = 0;
            $bsPagoMovil = 0;

            foreach ($metodoPago as $payment) {
                switch ($payment['method']) {
                    case 'dol_efectivo':
                        $dolEfectivo -= $payment['amount'] - $payment['change'];
                        break;
                    case 'zelle':
                        $zelle -= $payment['amount'] - $payment['change'];
                        break;
                    case 'bs_efectivo':
                        $bsEfectivo -= ($payment['amount'] - $payment['change']);
                        break;
                    case 'bs_punto_de_venta':
                        $bsPuntoDeVenta -= ($payment['amount'] - $payment['change']);
                        break;
                    case 'bs_pago_movil':
                        $bsPagoMovil -= ($payment['amount'] - $payment['change']);
                        break;
                }
            }

            $cierreDeCaja->decrement('monto_total', $montoTotal);
            $cierreDeCaja->decrement('dol_efectivo', $dolEfectivo);
            $cierreDeCaja->decrement('zelle', $zelle);
            $cierreDeCaja->decrement('bs_efectivo', $bsEfectivo);
            $cierreDeCaja->decrement('bs_punto_de_venta', $bsPuntoDeVenta);
            $cierreDeCaja->decrement('bs_pago_movil', $bsPagoMovil);
        }

        $venta->estado = 'Anulada';
        $venta->save();
        return response()->json(null, 204);
    }
}
