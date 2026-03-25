<?php

namespace App\Services;

use App\Exceptions\HistorialNotFoundException;
use App\Models\HistorialAccion;
use App\Models\HistorialUbicacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class HistorialService
{
    public function allAcciones(array $filters = []): Collection
    {
        $limit = (int) ($filters['per_page'] ?? 100);

        return $this->buildFilteredQueryAcciones($filters)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function allUbicaciones(array $filters = []): Collection
    {
        $limit = (int) ($filters['per_page'] ?? 100);

        return $this->buildFilteredQueryUbicaciones($filters)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function allUbicacionesForPdf(array $filters = []): Collection
    {
        return $this->buildFilteredQueryUbicaciones($filters)
            ->orderBy('id')
            ->get();
    }

    public function findAccion(int $id): HistorialAccion
    {
        $historial = HistorialAccion::with('chasis')->find($id);

        if (! $historial) {
            throw new HistorialNotFoundException($id);
        }

        return $historial;
    }

    public function findUbicacion(int $id): HistorialUbicacion
    {
        $historial = HistorialUbicacion::with('chasis')->find($id);

        if (! $historial) {
            throw new HistorialNotFoundException($id);
        }

        return $historial;
    }

    public function recordAccionApp(int $chasisId, string $accion, string $descripcion, array $detalle = []): HistorialAccion
    {
        return HistorialAccion::create([
            'chasis_id' => $chasisId,
            'chasis_referencia_id' => $chasisId,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'detalle' => $detalle,
        ]);
    }

    public function recordMovimiento(int $chasisId, string $descripcion, array $detalle = []): HistorialUbicacion
    {
        return HistorialUbicacion::create([
            'chasis_id' => $chasisId,
            'chasis_referencia_id' => $chasisId,
            'accion' => 'movimiento_ubicacion',
            'descripcion' => $descripcion,
            'detalle' => $detalle,
        ]);
    }

    private function buildFilteredQueryAcciones(array $filters): Builder
    {
        $query = HistorialAccion::query()->with('chasis');

        if (! empty($filters['chasis_id'])) {
            $chasisId = (int) $filters['chasis_id'];
            $query->where(function ($subQuery) use ($chasisId): void {
                $subQuery->where('chasis_id', $chasisId)
                    ->orWhere('chasis_referencia_id', $chasisId);
            });
        }

        if (! empty($filters['accion'])) {
            $query->where('accion', (string) $filters['accion']);
        }

        return $query;
    }

    private function buildFilteredQueryUbicaciones(array $filters): Builder
    {
        $query = HistorialUbicacion::query()->with('chasis');

        if (! empty($filters['chasis_id'])) {
            $chasisId = (int) $filters['chasis_id'];
            $query->where(function ($subQuery) use ($chasisId): void {
                $subQuery->where('chasis_id', $chasisId)
                    ->orWhere('chasis_referencia_id', $chasisId);
            });
        }

        return $query;
    }
}
