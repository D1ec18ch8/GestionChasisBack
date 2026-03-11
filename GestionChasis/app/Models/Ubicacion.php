<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Chasis;

class Ubicacion extends Model
{
    use HasFactory;

    protected $table = 'ubicaciones';

    protected $fillable = [
        'codigo',
        'razon_social',
        'aduana',
        'direccion',
        'telefono',
        'fax',
        'email',
        'nombre',
    ];

    public function chasis(): HasMany
    {
        return $this->hasMany(Chasis::class, 'ubicacion_id');
    }
}
