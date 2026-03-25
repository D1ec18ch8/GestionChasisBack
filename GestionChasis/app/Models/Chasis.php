<?php

namespace App\Models;

use App\Models\Estado;
use App\Models\HistorialAccion;
use App\Models\HistorialUbicacion;
use App\Models\TipoChasis;
use App\Models\Ubicacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chasis extends Model
{
    use HasFactory;

    protected $table = 'chasis';

    protected $fillable = [
        'tipo_chasis_id',
        'ubicacion_id',
        'estado_id',
        'nombre',
        'categoria',
        'numero',
        'averia_patas',
        'averia_luces',
        'averia_manoplas',
        'averia_mangueras',
        'averia_llantas',
        'placa',
    ];

    protected $appends = [
        'estado_actual',
        'equipamientos_en_mal_estado',
    ];

    protected $hidden = [
        'estado',
    ];

    protected $casts = [
        'numero' => 'integer',
        'estado_id' => 'integer',
        'averia_patas' => 'boolean',
        'averia_luces' => 'boolean',
        'averia_manoplas' => 'boolean',
        'averia_mangueras' => 'boolean',
        'averia_llantas' => 'boolean',
    ];

    public function tipoChasis(): BelongsTo
    {
        return $this->belongsTo(TipoChasis::class, 'tipo_chasis_id');
    }

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
    }

    public function estadoModel(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function historialAcciones(): HasMany
    {
        return $this->hasMany(HistorialAccion::class, 'chasis_id');
    }

    public function historialUbicaciones(): HasMany
    {
        return $this->hasMany(HistorialUbicacion::class, 'chasis_id');
    }

    public function getEquipamientosEnMalEstadoAttribute(): array
    {
        $malEstado = [];

        if ($this->averia_patas) {
            $malEstado[] = 'patas';
        }

        if ($this->averia_luces) {
            $malEstado[] = 'luces';
        }

        if ($this->averia_manoplas) {
            $malEstado[] = 'manoplas';
        }

        if ($this->averia_mangueras) {
            $malEstado[] = 'mangueras';
        }

        if ($this->averia_llantas) {
            $malEstado[] = 'llantas';
        }

        return $malEstado;
    }

    public function getEstadoActualAttribute(): ?string
    {
        return $this->estadoModel?->slug;
    }
}
