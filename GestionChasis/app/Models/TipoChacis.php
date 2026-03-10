<?php

namespace App\Models;

use App\Models\Chacis;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoChacis extends Model
{
    use HasFactory;

    protected $table = 'tipo_chacis';

    protected $fillable = [
        'nombre',
    ];

    public function chacis(): HasMany
    {
        return $this->hasMany(Chacis::class, 'tipo_chacis_id');
    }
}
