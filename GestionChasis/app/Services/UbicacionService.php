<?php

namespace App\Services;

use App\Http\Requests\StoreUbicacionRequest;
use App\Http\Requests\UpdateUbicacionRequest;
use App\Models\Ubicacion;
use Illuminate\Database\Eloquent\Collection;

class UbicacionService
{
    public function all(): Collection
    {
        return Ubicacion::with('chasis')->get();
    }

    public function create(StoreUbicacionRequest $request): Ubicacion
    {
        return Ubicacion::create($request->validated());
    }

    public function find(int $id): Ubicacion
    {
        return Ubicacion::with('chasis')->findOrFail($id);
    }

    public function update(Ubicacion $ubicacion, UpdateUbicacionRequest $request): Ubicacion
    {
        $ubicacion->update($request->validated());

        return $ubicacion->load('chasis');
    }

    public function delete(Ubicacion $ubicacion): void
    {
        $ubicacion->delete();
    }
}
