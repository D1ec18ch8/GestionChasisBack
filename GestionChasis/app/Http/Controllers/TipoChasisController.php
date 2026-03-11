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
        return response()->json($this->tipoChasisService->create($request), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->tipoChasisService->find($id));
    }

    public function update(UpdateTipoChasisRequest $request, TipoChasis $tipoChasis): JsonResponse
    {
        return response()->json($this->tipoChasisService->update($tipoChasis, $request));
    }

    public function destroy(TipoChasis $tipoChasis): JsonResponse
    {
        $this->tipoChasisService->delete($tipoChasis);

        return response()->json(null, 204);
    }
}
