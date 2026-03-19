<?php

namespace App\Services;

use App\Exceptions\EstadoInUseException;
use App\Exceptions\EstadoNotFoundException;
use App\Exceptions\ProtectedEstadoException;
use App\Http\Requests\StoreEstadoRequest;
use App\Http\Requests\UpdateEstadoRequest;
use App\Models\Estado;
use Illuminate\Database\Eloquent\Collection;

class EstadoService
{
    private const BASE_ESTADOS = ['optimo', 'revision'];

    public function all(): Collection
    {
        return Estado::with('chasis')->get();
    }

    public function create(StoreEstadoRequest $request): Estado
    {
        return Estado::create($request->validated());
    }

    public function find(int $id): Estado
    {
        $estado = Estado::with('chasis')->find($id);

        if (! $estado) {
            throw new EstadoNotFoundException($id);
        }

        return $estado;
    }

    public function update(Estado $estado, UpdateEstadoRequest $request): Estado
    {
        if ($this->isBaseEstado($estado)) {
            throw new ProtectedEstadoException($estado->slug);
        }

        $estado->update($request->validated());

        return $estado->load('chasis');
    }

    public function delete(Estado $estado): void
    {
        if ($this->isBaseEstado($estado)) {
            throw new ProtectedEstadoException($estado->slug);
        }

        if ($estado->chasis()->exists()) {
            throw new EstadoInUseException($estado->id);
        }

        $estado->delete();
    }

    private function isBaseEstado(Estado $estado): bool
    {
        return in_array($estado->slug, self::BASE_ESTADOS, true);
    }
}
