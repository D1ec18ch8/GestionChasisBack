<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chacis extends Model
{
    use HasFactory;

    protected $table = 'chacis';

    protected $fillable = [
        'tipo_chacis_id',
        'revision',
        'patas',
        'luces',
        'mangueras',
        'llantas',
    ];

    protected $casts = [
        'revision' => 'boolean',
    ];

    public function tipoChacis(): BelongsTo
    {
        return $this->belongsTo(TipoChacis::class, 'tipo_chacis_id');
    }
}
