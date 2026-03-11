<?php

use App\Http\Controllers\ChasisController;
use App\Http\Controllers\TipoChasisController;
use App\Http\Controllers\UbicacionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('chasis', ChasisController::class)
    ->parameters(['chasis' => 'chasis']);

Route::apiResource('tipos-chasis', TipoChasisController::class)
    ->parameters(['tipos-chasis' => 'tipoChasis']);

Route::apiResource('ubicaciones', UbicacionController::class)
    ->parameters(['ubicaciones' => 'ubicacion']);
