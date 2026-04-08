<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChasisController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\TipoChasisController;
use App\Http\Controllers\UbicacionController;
use Illuminate\Support\Facades\Route;

// Health check (sin autenticación)
Route::get('ping', function () {
    return response()->json([
        'message' => 'pong',
        'app' => 'GestionChasis',
    ]);
});

// Autenticación (sin protección)
Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');

// Rutas protegidas con JWT
Route::middleware('auth:api')->group(function () {
    // Autenticación protegida
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');
    Route::put('auth/profile', [AuthController::class, 'updateProfile'])->name('auth.profile.update');

    // Recursos principales
    Route::apiResource('chasis', ChasisController::class)
        ->parameters(['chasis' => 'chasis']);

    Route::apiResource('tipos-chasis', TipoChasisController::class)
        ->parameters(['tipos-chasis' => 'tipoChasis']);

    Route::apiResource('ubicaciones', UbicacionController::class)
        ->parameters(['ubicaciones' => 'ubicacion']);

    Route::apiResource('estados', EstadoController::class)
        ->parameters(['estados' => 'estado']);

    // Historial
    Route::get('historial/acciones', [HistorialController::class, 'acciones'])->name('historial.acciones');
    Route::get('historial/movimientos', [HistorialController::class, 'movimientos'])->name('historial.movimientos');
    Route::get('historial/movimientos/pdf', [HistorialController::class, 'exportMovimientosPdf'])->name('historial.movimientos.export-pdf');
});
