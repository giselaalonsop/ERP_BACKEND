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

Route::apiResource('ventas', VentaController::class);
Route::apiResource('venta-detalles', VentaDetalleController::class);
Route::apiResource('clientes', ClienteController::class);
// Route::post('/productos', [ProductoController::class, 'store']);
Route::middleware('role:admin')->group(function () {
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::get('/productos', [ProductoController::class, 'index']);
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::get('/categorias', [CategoriaController::class, 'index']);
    Route::post('productos/{producto}/cargar', [ProductoController::class, 'cargarInventario']);
    Route::post('productos/{producto}/descargar', [ProductoController::class, 'descargarInventario']);
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::put('/users/{id}', [RegisteredUserController::class, 'update']);
    Route::delete('/users/{id}', [AuthenticatedSessionController::class, 'delete']);
    Route::get('/clientes/{cedula}/historial', [ClienteController::class, 'historialCompras']);
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update']);
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy']);
    Route::get('/clientes/{cedula}/historial', [ClienteController::class, 'historialCompras']);
    Route::get('/venta-detalles', [VentaDetalleController::class, 'index']);
    Route::post('/venta-detalles', [VentaDetalleController::class, 'store']);
    Route::get('/venta-detalles/{ventaDetalle}', [VentaDetalleController::class, 'show']);
    Route::put('/venta-detalles/{ventaDetalle}', [VentaDetalleController::class, 'update']);
    Route::delete('/venta-detalles/{ventaDetalle}', [VentaDetalleController::class, 'destroy']);
});
Route::get('/users', [AuthenticatedSessionController::class, 'getUsers']);

Route::middleware(['auth:sanctum', 'check.permission:registrarUsuarios'])->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']);
});
