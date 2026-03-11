<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChasisRequest;
use App\Http\Requests\UpdateChasisRequest;
use App\Models\Chasis;
use App\Services\ChasisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class ChasisController extends BaseController
{
    public function __construct(private readonly ChasisService $chasisService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->chasisService->all());
    }

    public function store(StoreChasisRequest $request): JsonResponse
    {
        return response()->json($this->chasisService->create($request), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->chasisService->find($id));
    }

    public function update(UpdateChasisRequest $request, Chasis $chasis): JsonResponse
    {
        return response()->json($this->chasisService->update($chasis, $request));
    }

    public function destroy(Chasis $chasis): JsonResponse
    {
        $this->chasisService->delete($chasis);

        return response()->json(null, 204);
    }
}
