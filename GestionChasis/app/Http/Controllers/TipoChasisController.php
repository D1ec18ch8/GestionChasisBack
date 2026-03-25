<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTipoChasisRequest;
use App\Http\Requests\UpdateTipoChasisRequest;
use App\Models\TipoChasis;
use App\Services\TipoChasisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class TipoChasisController extends BaseController
{
    public function __construct(private readonly TipoChasisService $tipoChasisService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->tipoChasisService->all());
    }

    public function store(StoreTipoChasisRequest $request): JsonResponse
    {
        $tipoChasis = $this->tipoChasisService->create($request);

        return response()->json([
            'message' => 'Tipo de chasis creado exitosamente.',
            ...$tipoChasis->toArray(),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->tipoChasisService->find($id));
    }

    public function update(UpdateTipoChasisRequest $request, TipoChasis $tipoChasis): JsonResponse
    {
        $tipoActualizado = $this->tipoChasisService->update($tipoChasis, $request);

        return response()->json([
            'message' => 'Tipo de chasis actualizado exitosamente.',
            ...$tipoActualizado->toArray(),
        ]);
    }

    public function destroy(TipoChasis $tipoChasis): JsonResponse
    {
        $this->tipoChasisService->delete($tipoChasis);

        return response()->json(null, 204);
    }
}
