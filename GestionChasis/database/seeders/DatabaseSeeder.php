<?php

namespace Database\Seeders;

use App\Models\Chasis;
use App\Models\TipoChasis;
use App\Models\Ubicacion;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $tipoChasis = TipoChasis::firstOrCreate([
            'nombre' => 'TCF',
        ]);

        $ubicacion = Ubicacion::firstOrCreate(
            ['nombre' => 'Uvita'],
            [
                'codigo' => 'A121',
                'razon_social' => 'Almacen de Deposito Fiscal Cariari, S.A.',
                'aduana' => '005',
                'direccion' => 'Costado norte de Mall Cariari',
                'telefono' => '2293-4111',
                'fax' => '2239-3361',
                'email' => 'info@almacencariari.com',
            ]
        );

        Chasis::firstOrCreate(
            ['numero' => 726],
            [
            'tipo_chasis_id' => $tipoChasis->id,
                'ubicacion_id' => $ubicacion->id,
                'nombre' => 'Chasis TCF',
                'categoria' => '40 x 2',
                'estado' => 'operativo',
                'placa' => '19283',
            ]
        );
    }
}
