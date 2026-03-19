<?php

namespace App\Services;

use App\Exceptions\ChasisNotFoundException;
use App\Http\Requests\StoreChasisRequest;
use App\Http\Requests\UpdateChasisRequest;
use App\Models\Chasis;
use App\Models\Estado;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use RuntimeException;

class ChasisService
{
    public function all(array $filters = []): LengthAwarePaginator
    {
        $query = Chasis::query()->with(['tipoChasis', 'ubicacion', 'estadoModel']);

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);

            $query->where(function ($subQuery) use ($search): void {
                $subQuery->where('nombre', 'like', "%{$search}%")
                    ->orWhere('categoria', 'like', "%{$search}%")
                    ->orWhere('placa', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['estado'])) {
            $estado = (string) $filters['estado'];
            $query->whereHas('estadoModel', function ($subQuery) use ($estado): void {
                $subQuery->where('slug', $estado);
            });
        }

        if (! empty($filters['tipo_chasis_id'])) {
            $query->where('tipo_chasis_id', (int) $filters['tipo_chasis_id']);
        }

        if (! empty($filters['ubicacion_id'])) {
            $query->where('ubicacion_id', (int) $filters['ubicacion_id']);
        }

        if (! empty($filters['equipamiento_mal'])) {
            $column = match ((string) $filters['equipamiento_mal']) {
                'patas' => 'averia_patas',
                'luces' => 'averia_luces',
                'manoplas' => 'averia_manoplas',
                'mangueras' => 'averia_mangueras',
                'llantas' => 'averia_llantas',
            };

            $query->where($column, true);
        }

        $perPage = (int) ($filters['per_page'] ?? 15);

        return $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(StoreChasisRequest $request): Chasis
    {
        $data = $this->resolveEstadoData($request->validated());

        return Chasis::create($data)->load(['tipoChasis', 'ubicacion', 'estadoModel']);
    }

    public function find(int $id): Chasis
    {
        $chasis = Chasis::with(['tipoChasis', 'ubicacion', 'estadoModel'])->find($id);

        if (! $chasis) {
            throw new ChasisNotFoundException($id);
        }

        return $chasis;
    }

    public function update(Chasis $chasis, UpdateChasisRequest $request): Chasis
    {
        $data = $request->validated();
        $data = $this->resolveEstadoData([
            ...$data,
            'averia_patas' => $data['averia_patas'] ?? $chasis->averia_patas,
            'averia_luces' => $data['averia_luces'] ?? $chasis->averia_luces,
            'averia_manoplas' => $data['averia_manoplas'] ?? $chasis->averia_manoplas,
            'averia_mangueras' => $data['averia_mangueras'] ?? $chasis->averia_mangueras,
            'averia_llantas' => $data['averia_llantas'] ?? $chasis->averia_llantas,
        ]);

        $chasis->update($data);

        return $chasis->load(['tipoChasis', 'ubicacion', 'estadoModel']);
    }

    public function delete(Chasis $chasis): void
    {
        $chasis->delete();
    }

    private function resolveEstadoData(array $data): array
    {
        $hasAnyAveria = (bool) ($data['averia_patas'] ?? false)
            || (bool) ($data['averia_luces'] ?? false)
            || (bool) ($data['averia_manoplas'] ?? false)
            || (bool) ($data['averia_mangueras'] ?? false)
            || (bool) ($data['averia_llantas'] ?? false);

        $slug = $hasAnyAveria ? 'revision' : 'optimo';
        $estado = Estado::query()->where('slug', $slug)->first();

        if (! $estado) {
            throw new RuntimeException("No existe un estado base con slug {$slug}.");
        }

        $data['estado_id'] = $estado->id;

        return $data;
    }
}
