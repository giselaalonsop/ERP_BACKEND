<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function getReportData(Request $request)
    {
        Log::info('getReportData called');

        $startDateRaw = $request->query('start_date');
        $endDateRaw = $request->query('end_date');
        $location = $request->query('location');

        try {
            // Intentar convertir las fechas con Carbon
            $startDate = Carbon::createFromFormat('D M d Y H:i:s e+', $startDateRaw)->startOfDay();
            $endDate = Carbon::createFromFormat('D M d Y H:i:s e+', $endDateRaw)->endOfDay();
        } catch (\Exception $e) {
            Log::error('Error parsing dates', ['start_date' => $startDateRaw, 'end_date' => $endDateRaw, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        Log::info('Parsed dates', ['start_date' => $startDate, 'end_date' => $endDate, 'location' => $location]);

        try {
            // Consultas a la base de datos...
            $topCategorias = DB::table('productos')
                ->join('venta_detalles', 'productos.codigo_barras', '=', 'venta_detalles.codigo_barras')
                ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                ->where('productos.ubicacion', $location)
                ->where('ventas.estado', '!=', 'Anulada') // Excluir devoluciones
                ->whereBetween('ventas.created_at', [$startDate, $endDate])
                ->select('productos.categoria', DB::raw('SUM(venta_detalles.cantidad) as total_ventas'))
                ->groupBy('productos.categoria')
                ->orderBy('total_ventas', 'desc')
                ->limit(5)
                ->get();

            Log::info('Top Categorias', ['data' => $topCategorias]);

            $topProducto = DB::table('venta_detalles')
                ->join('productos', 'venta_detalles.codigo_barras', '=', 'productos.codigo_barras')
                ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                ->where('productos.ubicacion', $location)
                ->where('ventas.estado', '!=', 'Anulada') // Excluir devoluciones
                ->whereBetween('ventas.created_at', [$startDate, $endDate])
                ->select('productos.nombre', DB::raw('SUM(venta_detalles.cantidad) as total_vendido'))
                ->groupBy('productos.nombre')
                ->orderBy('total_vendido', 'desc')
                ->first();

            Log::info('Top Producto', ['data' => $topProducto]);

            $bottomProducto = DB::table('venta_detalles')
                ->join('productos', 'venta_detalles.codigo_barras', '=', 'productos.codigo_barras')
                ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                ->where('productos.ubicacion', $location)
                ->where('ventas.estado', '!=', 'Anulada') // Excluir devoluciones
                ->whereBetween('ventas.created_at', [$startDate, $endDate])
                ->select('productos.nombre', DB::raw('SUM(venta_detalles.cantidad) as total_vendido'))
                ->groupBy('productos.nombre')
                ->orderBy('total_vendido', 'asc')
                ->first();

            Log::info('Bottom Producto', ['data' => $bottomProducto]);

            $topCliente = DB::table('ventas')
                ->join('clientes', 'ventas.cliente', '=', 'clientes.cedula')
                ->where('ventas.estado', '!=', 'Anulada') // Excluir devoluciones
                ->where('ventas.location', $location)
                ->whereBetween('ventas.created_at', [$startDate, $endDate])
                ->select('clientes.nombre', DB::raw('COUNT(ventas.id) as total_compras'))
                ->groupBy('clientes.nombre')
                ->orderBy('total_compras', 'desc')
                ->first();

            Log::info('Top Cliente', ['data' => $topCliente]);

            $ganancias = DB::table('venta_detalles')
                ->join('productos', 'venta_detalles.codigo_barras', '=', 'productos.codigo_barras')
                ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                ->where('productos.ubicacion', $location)
                ->where('ventas.estado', '!=', 'Anulada') // Excluir devoluciones
                ->whereBetween('ventas.created_at', [$startDate, $endDate])
                ->select(DB::raw('SUM(venta_detalles.cantidad * (productos.precio_compra * productos.porcentaje_ganancia / 100)) as total_ganancias'))
                ->first();

            Log::info('Ganancias', ['data' => $ganancias]);

            $capital = DB::table('productos')
                ->where('productos.ubicacion', $location)
                ->select(DB::raw('SUM(cantidad_en_stock * (precio_compra + (precio_compra * porcentaje_ganancia / 100))) as total_capital'))
                ->first();

            Log::info('Capital', ['data' => $capital]);

            $montoCompras = DB::table('compras')
                ->whereBetween('compras.created_at', [$startDate, $endDate])
                ->select(DB::raw('SUM(monto_total) as total_compras'))
                ->first();

            Log::info('Monto Compras', ['data' => $montoCompras]);

            $ventasAnuales = DB::table('ventas')
                ->whereYear('created_at', '=', date('Y'))
                ->where('estado', '!=', 'Anulada') // Excluir devoluciones
                ->where('ventas.location', $location)
                ->select(DB::raw('COUNT(id) as total_ventas_cantidad, SUM(total_venta_dol) as total_ventas_anuales'))
                ->first();

            Log::info('Ventas Anuales', ['data' => $ventasAnuales]);

            $ventasMensuales = DB::table('ventas')
                ->whereMonth('created_at', '=', date('m'))
                ->whereYear('created_at', '=', date('Y'))
                ->where('estado', '!=', 'Anulada') // Excluir devoluciones
                ->where('ventas.location', $location)
                ->select(DB::raw('COUNT(id) as total_ventas_cantidad, SUM(total_venta_dol) as total_ventas_mensuales'))
                ->first();

            Log::info('Ventas Mensuales', ['data' => $ventasMensuales]);

            $ventasSemanales = DB::table('ventas')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->where('estado', '!=', 'Anulada') // Excluir devoluciones
                ->where('ventas.location', $location)
                ->select(DB::raw('COUNT(id) as total_ventas_cantidad, SUM(total_venta_dol) as total_ventas_semanales'))
                ->first();

            Log::info('Ventas Semanales', ['data' => $ventasSemanales]);

            // Ventas en el rango de fechas seleccionado
            $ventasRango = DB::table('ventas')
                ->where('ventas.location', $location)
                ->where('estado', '!=', 'Anulada') // Excluir devoluciones
                ->whereBetween('ventas.created_at', [$startDate, $endDate])
                ->select(DB::raw('COUNT(id) as total_ventas_cantidad, SUM(total_venta_dol) as total_ventas_rango'))
                ->first();

            Log::info('Ventas en el rango', ['data' => $ventasRango]);

            // Ganancias en el rango de fechas seleccionado
            $gananciasRango = DB::table('venta_detalles')
                ->join('productos', 'venta_detalles.codigo_barras', '=', 'productos.codigo_barras')
                ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                ->where('productos.ubicacion', $location)
                ->where('ventas.estado', '!=', 'Anulada') // Excluir devoluciones
                ->whereBetween('ventas.created_at', [$startDate, $endDate])
                ->select(DB::raw('SUM(venta_detalles.cantidad * (productos.precio_compra * productos.porcentaje_ganancia / 100)) as total_ganancias_rango'))
                ->first();

            Log::info('Ganancias en el rango', ['data' => $gananciasRango]);

            // Productos agotados
            $productosAgotados = DB::table('productos')
                ->where('cantidad_en_stock', '=', 0)
                ->where('ubicacion', $location)
                ->select('nombre', 'codigo_barras')
                ->get();

            Log::info('Productos Agotados', ['data' => $productosAgotados]);

            // Productos cerca de vencimiento (30 días o menos)
            $productosVencimiento = DB::table('productos')
                ->where('ubicacion', $location)
                ->whereBetween('fecha_caducidad', [now(), now()->addDays(30)])
                ->select('nombre', 'codigo_barras', 'fecha_caducidad')
                ->get();

            Log::info('Productos cerca de vencimiento', ['data' => $productosVencimiento]);

            // Para obtener el historial de ventas en el rango de fecha
            $historialVentas = DB::table('ventas')
                ->where('ventas.location', $location)
                ->where('estado', '!=', 'Anulada') // Excluir devoluciones
                ->whereBetween('ventas.created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(ventas.created_at) as fecha'), DB::raw('SUM(total_venta_dol) as total_ventas'))
                ->groupBy(DB::raw('DATE(ventas.created_at)'))
                ->get();

            Log::info('Historial de Ventas', ['data' => $historialVentas]);

            // Top 3 productos más vendidos
            $topProductos = DB::table('venta_detalles')
                ->join('productos', 'venta_detalles.codigo_barras', '=', 'productos.codigo_barras')
                ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                ->where('productos.ubicacion', $location)
                ->where('ventas.estado', '!=', 'Anulada') // Excluir devoluciones
                ->whereBetween('ventas.created_at', [$startDate, $endDate])
                ->select('productos.nombre', DB::raw('SUM(venta_detalles.cantidad) as total_vendido'))
                ->groupBy('productos.nombre')
                ->orderBy('total_vendido', 'desc')
                ->limit(3)
                ->get();

            Log::info('Top 3 Productos', ['data' => $topProductos]);

            $ventaPromedio = DB::table('ventas')
                ->where('location', $location)
                ->where('estado', '!=', 'Anulada') // Excluir devoluciones
                ->avg('total_venta_dol');

            Log::info('Venta Promedio', ['data' => $ventaPromedio]);

            // Pagos pendientes (compras con estado pendiente)
            $pagosPendientes = DB::table('compras')
                ->where('estado', 'pendiente')
                ->select(DB::raw('SUM(monto_total) as total_pendiente, COUNT(*) as cantidad_pendiente'))
                ->first();

            Log::info('Pagos Pendientes', ['data' => $pagosPendientes]);

            // Productos vencidos
            $productosVencidos = DB::table('productos')
                ->where('ubicacion', $location)
                ->whereDate('fecha_caducidad', '<', now())
                ->select('nombre', 'codigo_barras', 'fecha_caducidad')
                ->get();

            Log::info('Productos Vencidos', ['data' => $productosVencidos]);

            // Cobros pendientes (ventas con estado pendiente)
            $cobrosPendientes = DB::table('ventas')
                ->where('estado', 'pendiente')
                ->where('estado', '!=', 'Anulada') // Excluir devoluciones
                ->select(DB::raw('SUM(total_venta_dol) as total_pendiente, COUNT(*) as cantidad_pendiente'))
                ->first();

            Log::info('Cobros Pendientes', ['data' => $cobrosPendientes]);

            // Clientes que tienen más de un mes sin ir
            $clientesInactivos = DB::table('clientes')
                ->join('ventas', 'clientes.cedula', '=', 'ventas.cliente')
                ->where('ventas.location', $location)
                ->where('ventas.estado', '!=', 'Anulada') // Excluir devoluciones
                ->whereDate('ventas.created_at', '<', now()->subMonth())
                ->select('clientes.nombre', 'clientes.cedula', DB::raw('MAX(ventas.created_at) as ultima_compra'))
                ->groupBy('clientes.nombre', 'clientes.cedula')
                ->havingRaw('MAX(ventas.created_at) < ?', [now()->subMonth()])
                ->get()
                ->map(function ($item) {
                    $item->ultima_compra = Carbon::parse($item->ultima_compra)->format('Y-m-d');
                    return $item;
                });

            Log::info('Clientes Inactivos', ['data' => $clientesInactivos]);

            $totalDays = $startDate->diffInDays($endDate) + 1;

            $ventasDiariasPromedio = DB::table('ventas')
                ->where('ventas.location', $location)
                ->where('ventas.estado', '!=', 'Anulada') // Excluir devoluciones
                ->whereBetween('ventas.created_at', [$startDate, $endDate])
                ->select(DB::raw('SUM(total_venta_dol) / ' . $totalDays . ' as promedio_diario'))
                ->first();

            $totalWeeks = ceil($totalDays / 7);

            $ventasSemanalesPromedio = DB::table('ventas')
                ->where('ventas.location', $location)
                ->where('ventas.estado', '!=', 'Anulada') // Excluir devoluciones
                ->whereBetween('ventas.created_at', [$startDate, $endDate])
                ->select(DB::raw('SUM(total_venta_dol) / ' . $totalWeeks . ' as promedio_semanal'))
                ->first();

            // Devoluciones en el rango de fechas
            $devoluciones = DB::table('ventas')
                ->where('ventas.location', $location)
                ->where('estado', 'Anulada') // Solo devoluciones
                ->whereBetween('ventas.created_at', [$startDate, $endDate])
                ->select(DB::raw('COUNT(id) as total_devoluciones, SUM(total_venta_dol) as total_devoluciones_monto'))
                ->first();

            Log::info('Devoluciones', ['data' => $devoluciones]);

            return response()->json([
                'topCategorias' => $topCategorias->isEmpty() ? [] : $topCategorias,
                'topProducto' => $topProducto ?? (object) ['nombre' => 'N/A', 'total_vendido' => 0],
                'bottomProducto' => $bottomProducto ?? (object) ['nombre' => 'N/A', 'total_vendido' => 0],
                'topCliente' => $topCliente ?? (object) ['nombre' => 'N/A', 'total_compras' => 0],
                'ganancias' => $ganancias->total_ganancias ?? 0,
                'capital' => $capital->total_capital ?? 0,
                'montoCompras' => $montoCompras->total_compras ?? 0,
                'ventasAnuales' => [
                    'total_ventas_cantidad' => $ventasAnuales->total_ventas_cantidad ?? 0,
                    'total_ventas_anuales' => $ventasAnuales->total_ventas_anuales ?? 0,
                ],
                'ventasMensuales' => [
                    'total_ventas_cantidad' => $ventasMensuales->total_ventas_cantidad ?? 0,
                    'total_ventas_mensuales' => $ventasMensuales->total_ventas_mensuales ?? 0,
                ],
                'ventasSemanales' => [
                    'total_ventas_cantidad' => $ventasSemanales->total_ventas_cantidad ?? 0,
                    'total_ventas_semanales' => $ventasSemanales->total_ventas_semanales ?? 0,
                ],
                'ventasRango' => [
                    'total_ventas_cantidad' => $ventasRango->total_ventas_cantidad ?? 0,
                    'total_ventas_rango' => $ventasRango->total_ventas_rango ?? 0,
                ],
                'gananciasRango' => $gananciasRango->total_ganancias_rango ?? 0,
                'productosAgotados' => $productosAgotados->isEmpty() ? [] : $productosAgotados,
                'productosVencimiento' => $productosVencimiento->isEmpty() ? [] : $productosVencimiento,
                'historialVentas' => $historialVentas->isEmpty() ? [] : $historialVentas,
                'topProductos' => $topProductos->isEmpty() ? [] : $topProductos,
                'ventaPromedio' => $ventaPromedio ?? 0,
                'pagosPendientes' => [
                    'total' => $pagosPendientes->total_pendiente ?? 0,
                    'cantidad' => $pagosPendientes->cantidad_pendiente ?? 0,
                ],
                'productosVencidos' => $productosVencidos->isEmpty() ? [] : $productosVencidos,
                'cobrosPendientes' => [
                    'total' => $cobrosPendientes->total_pendiente ?? 0,
                    'cantidad' => $cobrosPendientes->cantidad_pendiente ?? 0,
                ],
                'clientesInactivos' => $clientesInactivos->isEmpty() ? [] : $clientesInactivos,
                'ventasDiariasPromedio' => $ventasDiariasPromedio->promedio_diario ?? 0,
                'ventasSemanalesPromedio' => $ventasSemanalesPromedio->promedio_semanal ?? 0,
                'devoluciones' => [
                    'total_devoluciones' => $devoluciones->total_devoluciones ?? 0,
                    'total_devoluciones_monto' => $devoluciones->total_devoluciones_monto ?? 0,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getReportData', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
