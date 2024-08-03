<?php

use App\Http\Controllers\CategoriaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\VentaDetalleController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\CierreDeCajaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ProveedorController;

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\NumeroDeCuentaController;
use App\Http\Controllers\ReportController;
use App\Models\Proveedor;
use App\Http\Controllers\FormaDeVentaController;
use App\Http\Controllers\UnidadDeMedidaController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('role:user')->group(function () {
    Route::get('/productos', [ProductoController::class, 'index']);
    Route::get('/analisis', [ReportController::class, 'getReportData']);
    Route::get('/categorias', [CategoriaController::class, 'index']);
    Route::get('/clientes/{cedula}/historial', [ClienteController::class, 'historialCompras']);
    Route::get('/clientes', [ClienteController::class, 'index']);
    Route::get('/clientes/{cedula}/historial', [ClienteController::class, 'historialCompras']);
    Route::get('/venta-detalles', [VentaDetalleController::class, 'index']);
    Route::get('/ventas/{id}', [VentaController::class, 'show']);
    Route::get('/ventas-pendientes', [VentaController::class, 'ventasPendientes']);
    Route::get('/ventas', [VentaController::class, 'index']);
    Route::get('/ventas-pendientes', [VentaController::class, 'ventasPendientes']);
    Route::get('/configuracion', [ConfiguracionController::class, 'getConfiguracion']);
    Route::get('/compras', [CompraController::class, 'index']);
    Route::get('/proveedores', [ProveedorController::class, 'index']);
    Route::get('/cierre-de-caja/{ubicacion}', [CierreDeCajaController::class, 'show']);
    Route::get('/clientes/{cedula}/ultimaCompra', [ClienteController::class, 'ultimaCompra']);
    Route::get('/unidadMedida', [UnidadDeMedidaController::class, 'index']);
    Route::get('/formasVenta', [FormaDeVentaController::class, 'index']);
});
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware('role:admin')->group(function () {
    Route::get('/analisis', [ReportController::class, 'getReportData']);
    Route::get('/logs', [AuditLogController::class, 'index']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::get('/productos', [ProductoController::class, 'index']);
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::get('/categorias', [CategoriaController::class, 'index']);
    Route::post('productos/{producto}/cargar', [ProductoController::class, 'cargarInventario']);
    Route::post('productos/{producto}/descargar', [ProductoController::class, 'descargarInventario']);
    Route::post('/productos/{producto}', [ProductoController::class, 'update']);
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']);
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::put('/users/{id}', [RegisteredUserController::class, 'update']);
    Route::put('/users/borrar/{id}', [AuthenticatedSessionController::class, 'deshabilitar']);
    Route::get('/clientes/{cedula}/historial', [ClienteController::class, 'historialCompras']);
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update']);
    Route::get('/clientes', [ClienteController::class, 'index']);
    Route::post('/clientes', [ClienteController::class, 'store']);
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy']);
    Route::get('/clientes/{cedula}/historial', [ClienteController::class, 'historialCompras']);
    Route::get('/venta-detalles', [VentaDetalleController::class, 'index']);
    Route::get('/ventas/{id}', [VentaController::class, 'show']);
    Route::get('/ventas-pendientes', [VentaController::class, 'ventasPendientes']);
    Route::get('/ventas', [VentaController::class, 'index']);
    Route::get('/ventas-pendientes', [VentaController::class, 'ventasPendientes']);
    Route::post('/ventas', [VentaController::class, 'store']);
    Route::put('/ventas/{venta}', [VentaController::class, 'update']);
    Route::delete('/ventas/{venta}', [VentaController::class, 'destroy']);
    Route::get('/proveedores', [ProveedorController::class, 'index']);
    Route::post('/proveedores', [ProveedorController::class, 'store']);
    Route::put('/proveedores/{proveedor}', [ProveedorController::class, 'update']);
    Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy']);
    Route::get('/compras', [CompraController::class, 'index']);
    Route::post('/compras', [CompraController::class, 'store']);
    Route::put('/compras/{compra}', [CompraController::class, 'update']);
    Route::delete('/compras/{compra}', [CompraController::class, 'destroy']);
    Route::post('compras/{compra}/abonar', [CompraController::class, 'abonar']);
    Route::get('/configuracion', [ConfiguracionController::class, 'getConfiguracion']);
    Route::post('/configuracion', [ConfiguracionController::class, 'store']);
    Route::put('/configuracion/{configuracion}', [ConfiguracionController::class, 'update']);
    Route::get('/cierre-de-caja/{ubicacion}', [CierreDeCajaController::class, 'show']);
    Route::post('/cierre-de-caja/registrar-venta', [CierreDeCajaController::class, 'registrarVenta']);
    Route::post('/cierre-de-caja/cerrar/{ubicacion}', [CierreDeCajaController::class, 'cerrarCaja']);
    Route::get('/cierre-de-caja', [CierreDeCajaController::class, 'index']);
    Route::get('/users', [AuthenticatedSessionController::class, 'getUsers']);
    Route::get('/clientes/{cedula}/ultimaCompra', [ClienteController::class, 'ultimaCompra']);
    Route::get('/productos/inhabilitados', [ProductoController::class, 'inhabilitados']);
    Route::post('/productos/{id}/habilitar', [ProductoController::class, 'habilitar']);
    Route::get('/clientes/inhabilitados', [ClienteController::class, 'inHabilitados']);
    Route::post('/clientes/{id}/habilitar', [ClienteController::class, 'habilitar']);
    Route::put('/categorias/habilitar/{id}', [CategoriaController::class, 'habilitar']);
    Route::get('/categorias/inhabilitadas', [CategoriaController::class, 'inHabilitados']);
    Route::delete('categorias/{categoria}', [CategoriaController::class, 'destroy']);
    Route::put('/categorias/{categoria}', [CategoriaController::class, 'update']);
    Route::put('/proveedor/habilitar/{id}', [ProveedorController::class, 'habilitar']);
    Route::get('/proveedores/inhabilitados', [ProveedorController::class, 'inHabilitados']);
    Route::get('/usuarios/inhabilitados', [AuthenticatedSessionController::class, 'inHabilitados']);
    Route::put('/usuarios/habilitar/{id}', [AuthenticatedSessionController::class, 'habilitar']);
    Route::get('/formasVenta', [FormaDeVentaController::class, 'index']);
    Route::post('/formasVenta', [FormaDeVentaController::class, 'store']);
    Route::put('/formasVenta/{formaDeVenta}', [FormaDeVentaController::class, 'update']);
    Route::delete('/formasVenta/{formaDeVenta}', [FormaDeVentaController::class, 'destroy']);
    Route::get('/unidadMedida', [UnidadDeMedidaController::class, 'index']);
    Route::post('/unidadMedida', [UnidadDeMedidaController::class, 'store']);
    Route::put('/unidadMedida/{unidadDeMedida}', [UnidadDeMedidaController::class, 'update']);
    Route::delete('/unidadMedida/{unidadDeMedida}', [UnidadDeMedidaController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'check.permission:registrarUsuarios'])->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']);
});
Route::middleware(['auth:sanctum', 'check.permission:facturacion'])->group(function () {
    Route::post('/ventas', [VentaController::class, 'store']);
    Route::put('/ventas/{venta}', [VentaController::class, 'update']);
    Route::delete('/ventas/{venta}', [VentaController::class, 'destroy']);
    Route::post('/cierre-de-caja/registrar-venta', [CierreDeCajaController::class, 'registrarVenta']);
    Route::post('/unidadMedida', [UnidadDeMedidaController::class, 'store']);
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::post('/formasVenta', [FormaDeVentaController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'check.permission:registrarProductos'])->group(function () {
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::post('productos/{producto}/cargar', [ProductoController::class, 'cargarInventario']);
    Route::post('productos/{producto}/descargar', [ProductoController::class, 'descargarInventario']);
    Route::post('/productos/{producto}', [ProductoController::class, 'update']);
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']);
});
Route::middleware(['auth:sanctum', 'check.permission:registrarClientes'])->group(function () {
    Route::post('/clientes', [ClienteController::class, 'store']);
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update']);
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy']);
});
Route::middleware(['auth:sanctum', 'check.permission:agregarProveedores'])->group(function () {
    Route::get('/proveedores', [ProveedorController::class, 'index']);
    Route::post('/proveedores', [ProveedorController::class, 'store']);
    Route::put('/proveedores/{proveedor}', [ProveedorController::class, 'update']);
    Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy']);
});
Route::middleware(['auth:sanctum', 'check.permission:registrarCompras'])->group(function () {
    Route::get('/compras', [CompraController::class, 'index']);
    Route::post('/compras', [CompraController::class, 'store']);
    Route::put('/compras/{compra}', [CompraController::class, 'update']);
    Route::delete('/compras/{compra}', [CompraController::class, 'destroy']);
    Route::post('compras/{compra}/abonar', [CompraController::class, 'abonar']);
});
Route::middleware(['auth:sanctum', 'check.permission:configuracion'])->group(function () {
    Route::get('/configuracion', [ConfiguracionController::class, 'getConfiguracion']);
    Route::post('/configuracion', [ConfiguracionController::class, 'store']);
    Route::put('/configuracion/{configuracion}', [ConfiguracionController::class, 'update']);
    Route::delete('categorias/{categoria}', [CategoriaController::class, 'destroy']);
    Route::post('/unidadMedida', [UnidadDeMedidaController::class, 'store']);
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::post('/formasVenta', [FormaDeVentaController::class, 'store']);
    Route::put('/unidadMedida/{unidadDeMedida}', [UnidadDeMedidaController::class, 'update']);
    Route::delete('/unidadMedida/{unidadDeMedida}', [UnidadDeMedidaController::class, 'destroy']);
    Route::put('/formasVenta/{formaDeVenta}', [FormaDeVentaController::class, 'update']);
    Route::delete('/formasVenta/{formaDeVenta}', [FormaDeVentaController::class, 'destroy']);
    Route::delete('categorias/{categoria}', [CategoriaController::class, 'destroy']);
    Route::put('/categorias/{categoria}', [CategoriaController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'check.permission:verUsuarios'])->group(function () {
    Route::get('/users', [AuthenticatedSessionController::class, 'getUsers']);
});
Route::middleware(['auth:sanctum', 'check.permission:cargaInventario'])->group(function () {
    Route::post('productos/{producto}/cargar', [ProductoController::class, 'cargarInventario']);
});
Route::middleware(['auth:sanctum', 'check.permission:descargaInventario'])->group(function () {
    Route::post('productos/{producto}/descargar', [ProductoController::class, 'descargarInventario']);
});
Route::middleware(['auth:sanctum', 'check.permission:cierreDeCaja'])->group(function () {
    Route::get('/cierre-de-caja/{ubicacion}', [CierreDeCajaController::class, 'show']);
    Route::post('/cierre-de-caja/cerrar/{ubicacion}', [CierreDeCajaController::class, 'cerrarCaja']);
    Route::get('/cierre-de-caja', [CierreDeCajaController::class, 'index']);
});
