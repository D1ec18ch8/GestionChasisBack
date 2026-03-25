<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historial extends Model
{
    use HasFactory;

    public const TIPO_ACCION_APP = 'accion_app';
    public const TIPO_MOVIMIENTO = 'movimiento';

    protected $table = 'historiales';

    protected $fillable = [
        'chasis_id',
        'chasis_referencia_id',
        'tipo_historial',
        'accion',
        'descripcion',
        'detalle',
    ];

    protected $casts = [
        'chasis_id' => 'integer',
        'chasis_referencia_id' => 'integer',
        'tipo_historial' => 'string',
        'detalle' => 'array',
    ];

    public function chasis(): BelongsTo
    {
        return $this->belongsTo(Chasis::class, 'chasis_id');
    }
}
