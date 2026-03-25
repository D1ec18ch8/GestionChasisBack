<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUbicacionRequest;
use App\Http\Requests\UpdateUbicacionRequest;
use App\Models\Ubicacion;
use App\Services\UbicacionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class UbicacionController extends BaseController
{
    public function __construct(private readonly UbicacionService $ubicacionService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->ubicacionService->all());
    }

    public function store(StoreUbicacionRequest $request): JsonResponse
    {
        $ubicacion = $this->ubicacionService->create($request);

        return response()->json([
            'message' => 'Ubicacion creada exitosamente.',
            ...$ubicacion->toArray(),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->ubicacionService->find($id));
    }

    public function update(UpdateUbicacionRequest $request, Ubicacion $ubicacion): JsonResponse
    {
        $ubicacionActualizada = $this->ubicacionService->update($ubicacion, $request);

        return response()->json([
            'message' => 'Ubicacion actualizada exitosamente.',
            ...$ubicacionActualizada->toArray(),
        ]);
    }

    public function destroy(Ubicacion $ubicacion): JsonResponse
    {
        $this->ubicacionService->delete($ubicacion);

        return response()->json(null, 204);
    }
}
