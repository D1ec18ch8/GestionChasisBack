<?php

namespace App\Services;

use App\Http\Requests\StoreTipoChasisRequest;
use App\Http\Requests\UpdateTipoChasisRequest;
use App\Models\TipoChasis;
use Illuminate\Database\Eloquent\Collection;

class TipoChasisService
{
    public function all(): Collection
    {
        return TipoChasis::with('chasis')->get();
    }

    public function create(StoreTipoChasisRequest $request): TipoChasis
    {
        return TipoChasis::create($request->validated());
    }

    public function find(int $id): TipoChasis
    {
        return TipoChasis::with('chasis')->findOrFail($id);
    }

    public function update(TipoChasis $tipoChasis, UpdateTipoChasisRequest $request): TipoChasis
    {
        $tipoChasis->update($request->validated());

        return $tipoChasis->load('chasis');
    }

    public function delete(TipoChasis $tipoChasis): void
    {
        $tipoChasis->delete();
    }
}
