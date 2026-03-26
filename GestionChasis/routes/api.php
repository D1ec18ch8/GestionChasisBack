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
    Route::get('historial', [HistorialController::class, 'index'])->name('historial.index');
    Route::get('historial/acciones', [HistorialController::class, 'acciones'])->name('historial.acciones');
    Route::get('historial/ubicaciones', [HistorialController::class, 'ubicaciones'])->name('historial.ubicaciones');
    Route::get('historial/movimientos', [HistorialController::class, 'ubicaciones'])->name('historial.movimientos');
    Route::get('historial/ubicaciones/pdf/general', [HistorialController::class, 'exportUbicacionesPdfGeneral'])->name('historial.ubicaciones.export-pdf-general');
    Route::get('historial/movimientos/pdf/general', [HistorialController::class, 'exportUbicacionesPdfGeneral'])->name('historial.movimientos.export-pdf-general');
    Route::get('historial/acciones/{id}', [HistorialController::class, 'showAccion'])->name('historial.acciones.show');
    Route::get('historial/ubicaciones/{id}', [HistorialController::class, 'showUbicacion'])->name('historial.ubicaciones.show');
    Route::get('historial/movimientos/pdf', [HistorialController::class, 'exportUbicacionesPdfGeneral'])->name('historial.movimientos.export-pdf');
    Route::get('historial/chasis/{id}/pdf', [HistorialController::class, 'exportUbicacionesByChasisPdf'])->name('historial.export-pdf');
    Route::get('historial/ubicaciones/chasis/{id}/pdf', [HistorialController::class, 'exportUbicacionesByChasisPdf'])->name('historial.ubicaciones.export-pdf');
    Route::get('chasis/{id}/historial', [HistorialController::class, 'byChasisAcciones'])->name('historial.by-chasis');
    Route::get('chasis/{id}/historial/acciones', [HistorialController::class, 'byChasisAcciones'])->name('historial.by-chasis-acciones');
    Route::get('chasis/{id}/historial/ubicaciones', [HistorialController::class, 'byChasisUbicaciones'])->name('historial.by-chasis-ubicaciones');
    Route::get('chasis/{id}/historial/movimientos', [HistorialController::class, 'byChasisUbicaciones'])->name('historial.by-chasis-movimientos');
});
