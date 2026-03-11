<?php

namespace App\Models;

use App\Models\Chasis;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoChasis extends Model
{
    use HasFactory;

    protected $table = 'tipo_chasis';

    protected $fillable = [
        'nombre',
    ];

    public function chasis(): HasMany
    {
        return $this->hasMany(Chasis::class, 'tipo_chasis_id');
    }
}
