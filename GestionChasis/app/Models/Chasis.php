<?php

namespace App\Models;

use App\Models\TipoChasis;
use App\Models\Ubicacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chasis extends Model
{
    use HasFactory;

    protected $table = 'chasis';

    protected $fillable = [
        'tipo_chasis_id',
        'ubicacion_id',
        'nombre',
        'categoria',
        'numero',
        'estado',
        'placa',
    ];

    protected $casts = [
        'numero' => 'integer',
    ];

    public function tipoChasis(): BelongsTo
    {
        return $this->belongsTo(TipoChasis::class, 'tipo_chasis_id');
    }

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
    }
}
