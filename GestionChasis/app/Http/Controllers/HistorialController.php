<?php

namespace App\Http\Controllers;

use App\Services\HistorialService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controller as BaseController;

class HistorialController extends BaseController
{
    public function __construct(private readonly HistorialService $historialService)
    {
    }

    public function acciones(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'accion' => ['sometimes', 'in:creacion,actualizacion,eliminacion'],
        ]);

        $registros = $this->historialService->allAcciones($filters);

        return response()->json([
            'total' => $registros->count(),
            'data' => $registros,
        ]);
    }

    public function movimientos(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'placa' => ['sometimes', 'string', 'max:50'],
        ]);

        $registros = $this->historialService->allUbicaciones($filters);

        return response()->json([
            'total' => $registros->count(),
            'data' => $registros,
        ]);
    }

    public function exportMovimientosPdf(Request $request): Response
    {
        $filters = $request->validate([
            'placa' => ['sometimes', 'string', 'max:50'],
        ]);

        $registros = $this->historialService->allUbicacionesForPdf($filters);
        $timezone = (string) config('app.timezone', 'UTC');
        $placa = isset($filters['placa']) ? trim((string) $filters['placa']) : null;
        $nombreArchivo = $placa
            ? 'historial-movimientos-' . preg_replace('/[^a-zA-Z0-9\-_]/', '-', $placa) . '.pdf'
            : 'historial-movimientos-general.pdf';

        $pdf = Pdf::loadView('historial.chasis-pdf', [
            'placa' => $placa,
            'timezone' => $timezone,
            'registros' => $registros,
            'generadoEn' => now($timezone)->format('Y-m-d H:i:s'),
        ]);

        return $pdf->download($nombreArchivo);
    }
}
