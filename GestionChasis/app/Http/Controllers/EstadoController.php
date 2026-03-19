<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEstadoRequest;
use App\Http\Requests\UpdateEstadoRequest;
use App\Models\Estado;
use App\Services\EstadoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class EstadoController extends BaseController
{
    public function __construct(private readonly EstadoService $estadoService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->estadoService->all());
    }

    public function store(StoreEstadoRequest $request): JsonResponse
    {
        return response()->json($this->estadoService->create($request), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->estadoService->find($id));
    }

    public function update(UpdateEstadoRequest $request, Estado $estado): JsonResponse
    {
        return response()->json($this->estadoService->update($estado, $request));
    }

    public function destroy(Estado $estado): JsonResponse
    {
        $this->estadoService->delete($estado);

        return response()->json(null, 204);
    }
}
