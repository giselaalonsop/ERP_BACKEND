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


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


// Route::apiResource('ventas', VentaController::class);
// Route::apiResource('venta-detalles', VentaDetalleController::class);
// Route::apiResource('clientes', ClienteController::class);


Route::middleware('role:admin')->group(function () {
    Route::get('/logs', [AuditLogController::class, 'index']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::get('/productos', [ProductoController::class, 'index']);
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::get('/categorias', [CategoriaController::class, 'index']);
    Route::post('productos/{producto}/cargar', [ProductoController::class, 'cargarInventario']);
    Route::post('productos/{producto}/descargar', [ProductoController::class, 'descargarInventario']);
    Route::put('/productos/{producto}', [ProductoController::class, 'update']);
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']);
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::put('/users/{id}', [RegisteredUserController::class, 'update']);
    Route::delete('/users/{id}', [AuthenticatedSessionController::class, 'delete']);
    Route::get('/clientes/{cedula}/historial', [ClienteController::class, 'historialCompras']);
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update']);
    Route::get('/clientes', [ClienteController::class, 'index']);
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy']);
    Route::get('/clientes/{cedula}/historial', [ClienteController::class, 'historialCompras']);
    Route::get('/venta-detalles', [VentaDetalleController::class, 'index']);
    Route::post('/venta-detalles', [VentaDetalleController::class, 'store']);
    Route::get('/venta-detalles/{ventaDetalle}', [VentaDetalleController::class, 'show']);
    Route::put('/venta-detalles/{ventaDetalle}', [VentaDetalleController::class, 'update']);
    Route::delete('/venta-detalles/{ventaDetalle}', [VentaDetalleController::class, 'destroy']);
    Route::apiResource('configuraciones', ConfiguracionController::class);
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
    Route::apiResource('/numeros-de-cuenta', NumeroDeCuentaController::class);
    Route::apiResource('/compras', CompraController::class);

    Route::get('/configuracion', [ConfiguracionController::class, 'getConfiguracion']);
    Route::post('/configuracion', [ConfiguracionController::class, 'store']);
    Route::put('/configuracion/{configuracion}', [ConfiguracionController::class, 'update']);
    Route::get('/cierre-de-caja/{ubicacion}', [CierreDeCajaController::class, 'show']);
    Route::post('/cierre-de-caja/registrar-venta', [CierreDeCajaController::class, 'registrarVenta']);
    Route::post('/cierre-de-caja/cerrar/{ubicacion}', [CierreDeCajaController::class, 'cerrarCaja']);
    Route::get('/cierre-de-caja', [CierreDeCajaController::class, 'index']);
});

Route::get('/users', [AuthenticatedSessionController::class, 'getUsers']);

Route::middleware(['auth:sanctum', 'check.permission:registrarUsuarios'])->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']);
});



Route::apiResource('proveedores', ProveedorController::class);

Route::apiResource('compras', CompraController::class);
Route::post('compras/{compra}/abonar', [CompraController::class, 'abonar']);
