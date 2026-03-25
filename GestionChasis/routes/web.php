<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'API de GestionChasis activa.',
        'api_base' => url('/api'),
        'health' => url('/api/ping'),
    ]);
});
