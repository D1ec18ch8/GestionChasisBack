<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChasisRequest;
use App\Http\Requests\UpdateChasisRequest;
use App\Models\Chasis;
use App\Services\ChasisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ChasisController extends BaseController
{
    public function __construct(private readonly ChasisService $chasisService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'string', 'max:255'],
            'estado' => ['sometimes', 'string', 'exists:estados,slug'],
            'tipo_chasis_id' => ['sometimes', 'integer', 'exists:tipo_chasis,id'],
            'ubicacion_id' => ['sometimes', 'integer', 'exists:ubicaciones,id'],
            'equipamiento_mal' => ['sometimes', 'in:patas,luces,manoplas,mangueras,llantas'],
        ]);

        return response()->json($this->chasisService->all($filters));
    }

    public function store(StoreChasisRequest $request): JsonResponse
    {
        $chasis = $this->chasisService->create($request);

        return response()->json([
            'message' => 'Chasis creado exitosamente.',
            ...$chasis->toArray(),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->chasisService->find($id));
    }

    public function update(UpdateChasisRequest $request, Chasis $chasis): JsonResponse
    {
        $chasisActualizado = $this->chasisService->update($chasis, $request);

        return response()->json([
            'message' => 'Chasis actualizado exitosamente.',
            ...$chasisActualizado->toArray(),
        ]);
    }

    public function destroy(Chasis $chasis): JsonResponse
    {
        $this->chasisService->delete($chasis);

        return response()->json(null, 204);
    }
}
