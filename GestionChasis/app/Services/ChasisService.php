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
    public function __construct(private readonly HistorialService $historialService)
    {
    }

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
        $chasis = Chasis::create($data);
        $chasisConRelaciones = $chasis->fresh()->load(['tipoChasis', 'ubicacion', 'estadoModel']);

        $this->historialService->recordAccionApp(
            $chasis->id,
            'creacion',
            'Se creo el chasis.',
            [
                'nuevo' => $this->snapshotForHistory($chasisConRelaciones),
            ]
        );

        return $chasisConRelaciones;
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
        $chasis->loadMissing(['tipoChasis', 'ubicacion', 'estadoModel']);
        $anterior = $this->snapshotForHistory($chasis);
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
        $chasisActualizado = $chasis->fresh()->load(['tipoChasis', 'ubicacion', 'estadoModel']);
        $nuevo = $this->snapshotForHistory($chasisActualizado);
        $cambios = $this->buildHumanReadableChanges($anterior, $nuevo);

        $descripcion = 'Se actualizo el chasis.';
        $huboCambioUbicacion = false;
        $ubicacionAntes = null;
        $ubicacionDespues = null;

        foreach ($cambios as $cambio) {
            if (($cambio['campo'] ?? '') === 'Ubicacion') {
                $descripcion = "El chasis {$chasis->id} paso de ubicacion {$cambio['antes']} a {$cambio['despues']}.";
                $huboCambioUbicacion = true;
                $ubicacionAntes = $cambio['antes'] ?? null;
                $ubicacionDespues = $cambio['despues'] ?? null;
                break;
            }
        }

        $this->historialService->recordAccionApp(
            $chasis->id,
            'actualizacion',
            $descripcion,
            [
                'anterior' => $anterior,
                'nuevo' => $nuevo,
                'cambios' => $cambios,
            ]
        );

        if ($huboCambioUbicacion) {
            $this->historialService->recordMovimiento(
                $chasis->id,
                "Movimiento de chasis {$chasisActualizado->nombre} ({$chasis->id}): {$ubicacionAntes} -> {$ubicacionDespues}.",
                [
                    'chasis_nombre' => $chasisActualizado->nombre,
                    'origen' => $ubicacionAntes,
                    'destino' => $ubicacionDespues,
                ]
            );
        }

        return $chasisActualizado;
    }

    public function delete(Chasis $chasis): void
    {
        $chasis->loadMissing(['tipoChasis', 'ubicacion', 'estadoModel']);
        $snapshot = $this->snapshotForHistory($chasis);

        $this->historialService->recordAccionApp(
            $chasis->id,
            'eliminacion',
            'Se elimino el chasis.',
            [
                'anterior' => $snapshot,
            ]
        );

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

    private function snapshotForHistory(Chasis $chasis): array
    {
        return [
            'id' => $chasis->id,
            'tipo_chasis_id' => $chasis->tipo_chasis_id,
            'tipo_chasis' => $chasis->tipoChasis?->nombre,
            'ubicacion_id' => $chasis->ubicacion_id,
            'ubicacion' => $chasis->ubicacion?->nombre,
            'estado_id' => $chasis->estado_id,
            'estado' => $chasis->estadoModel?->slug,
            'nombre' => $chasis->nombre,
            'categoria' => $chasis->categoria,
            'numero' => $chasis->numero,
            'placa' => $chasis->placa,
            'averia_patas' => (bool) $chasis->averia_patas,
            'averia_luces' => (bool) $chasis->averia_luces,
            'averia_manoplas' => (bool) $chasis->averia_manoplas,
            'averia_mangueras' => (bool) $chasis->averia_mangueras,
            'averia_llantas' => (bool) $chasis->averia_llantas,
        ];
    }

    private function buildHumanReadableChanges(array $anterior, array $nuevo): array
    {
        $mapa = [
            'tipo_chasis' => 'Tipo chasis',
            'ubicacion' => 'Ubicacion',
            'estado' => 'Estado',
            'nombre' => 'Nombre',
            'categoria' => 'Categoria',
            'numero' => 'Numero',
            'placa' => 'Placa',
            'averia_patas' => 'Averia patas',
            'averia_luces' => 'Averia luces',
            'averia_manoplas' => 'Averia manoplas',
            'averia_mangueras' => 'Averia mangueras',
            'averia_llantas' => 'Averia llantas',
        ];

        $cambios = [];
        foreach ($mapa as $clave => $etiqueta) {
            $antes = $anterior[$clave] ?? null;
            $despues = $nuevo[$clave] ?? null;

            if ($antes !== $despues) {
                $cambios[] = [
                    'campo' => $etiqueta,
                    'antes' => $antes,
                    'despues' => $despues,
                ];
            }
        }

        return $cambios;
    }
}
