<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialAccion extends Model
{
    use HasFactory;

    protected $table = 'historial_acciones';

    protected $fillable = [
        'chasis_id',
        'chasis_referencia_id',
        'accion',
        'descripcion',
        'detalle',
    ];

    protected $casts = [
        'chasis_id' => 'integer',
        'chasis_referencia_id' => 'integer',
        'detalle' => 'array',
    ];

    public function chasis(): BelongsTo
    {
        return $this->belongsTo(Chasis::class, 'chasis_id');
    }
}
