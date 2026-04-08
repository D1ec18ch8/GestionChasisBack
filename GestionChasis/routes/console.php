<?php

use App\Models\HistorialUbicacion;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('historial:cleanup-movimientos', function () {
    $eliminados = HistorialUbicacion::query()
        ->where('created_at', '<', Carbon::today())
        ->delete();

    $this->info("Registros eliminados: {$eliminados}");
})->purpose('Elimina movimientos de dias anteriores al actual.');

Schedule::command('historial:cleanup-movimientos')
    ->dailyAt('00:00')
    ->withoutOverlapping();
