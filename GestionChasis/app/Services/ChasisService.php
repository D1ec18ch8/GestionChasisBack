<?php

namespace App\Services;

use App\Http\Requests\StoreChasisRequest;
use App\Http\Requests\UpdateChasisRequest;
use App\Models\Chasis;
use Illuminate\Database\Eloquent\Collection;

class ChasisService
{
    public function all(): Collection
    {
        return Chasis::with(['tipoChasis', 'ubicacion'])->get();
    }

    public function create(StoreChasisRequest $request): Chasis
    {
        return Chasis::create($request->validated())->load(['tipoChasis', 'ubicacion']);
    }

    public function find(int $id): Chasis
    {
        return Chasis::with(['tipoChasis', 'ubicacion'])->findOrFail($id);
    }

    public function update(Chasis $chasis, UpdateChasisRequest $request): Chasis
    {
        $chasis->update($request->validated());

        return $chasis->load(['tipoChasis', 'ubicacion']);
    }

    public function delete(Chasis $chasis): void
    {
        $chasis->delete();
    }
}
