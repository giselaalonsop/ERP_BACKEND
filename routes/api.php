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
    Route::get('/users', [AuthenticatedSessionController::class, 'getUsers']);
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::put('/users/{id}', [AuthenticatedSessionController::class, 'update']);
    Route::delete('/users/{id}', [AuthenticatedSessionController::class, 'delete']);
});


// Route::middleware('role:admin')->group(function () {
//     Route::apiResource('productos', ProductoController::class, 'store');
//     Route::post('productos/{id}/cargar', [ProductoController::class, 'cargarInventario']);
//     Route::post('productos/{id}/descargar', [ProductoController::class, 'descargarInventario']);
// });
