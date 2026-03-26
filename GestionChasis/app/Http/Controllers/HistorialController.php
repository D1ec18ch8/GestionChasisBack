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

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'chasis_id' => ['sometimes', 'integer', 'min:1'],
            'accion' => ['sometimes', 'in:creacion,actualizacion,eliminacion'],
        ]);

        $registros = $this->historialService->allAcciones($filters);

        return response()->json([
            'total' => $registros->count(),
            'data' => $registros,
        ]);
    }

    public function acciones(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'chasis_id' => ['sometimes', 'integer', 'min:1'],
            'accion' => ['sometimes', 'in:creacion,actualizacion,eliminacion'],
        ]);

        $registros = $this->historialService->allAcciones($filters);

        return response()->json([
            'total' => $registros->count(),
            'data' => $registros,
        ]);
    }

    public function ubicaciones(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'chasis_id' => ['sometimes', 'integer', 'min:1'],
        ]);

        $registros = $this->historialService->allUbicaciones($filters);

        return response()->json([
            'total' => $registros->count(),
            'data' => $registros,
        ]);
    }

    public function showAccion(int $id): JsonResponse
    {
        return response()->json($this->historialService->findAccion($id));
    }

    public function showUbicacion(int $id): JsonResponse
    {
        return response()->json($this->historialService->findUbicacion($id));
    }

    public function byChasisAcciones(int $id, Request $request): JsonResponse
    {
        $filters = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'accion' => ['sometimes', 'in:creacion,actualizacion,eliminacion'],
        ]);

        $filters['chasis_id'] = $id;

        $registros = $this->historialService->allAcciones($filters);

        return response()->json([
            'total' => $registros->count(),
            'data' => $registros,
        ]);
    }

    public function byChasisUbicaciones(int $id, Request $request): JsonResponse
    {
        $filters = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $filters['chasis_id'] = $id;

        $registros = $this->historialService->allUbicaciones($filters);

        return response()->json([
            'total' => $registros->count(),
            'data' => $registros,
        ]);
    }

    public function exportUbicacionesByChasisPdf(int $id, Request $request): Response
    {
        $request->validate([]);
        $registros = $this->historialService->allUbicacionesForPdf();

        $pdf = Pdf::loadView('historial.chasis-pdf', [
            'chasisId' => null,
            'registros' => $registros,
            'generadoEn' => now()->format('Y-m-d H:i:s'),
        ]);

        return $pdf->download('historial-movimientos-general.pdf');
    }

    public function exportUbicacionesPdfGeneral(Request $request): Response
    {
        $request->validate([]);
        $registros = $this->historialService->allUbicacionesForPdf();

        $pdf = Pdf::loadView('historial.chasis-pdf', [
            'chasisId' => null,
            'registros' => $registros,
            'generadoEn' => now()->format('Y-m-d H:i:s'),
        ]);

        return $pdf->download('historial-movimientos-general.pdf');
    }
}
