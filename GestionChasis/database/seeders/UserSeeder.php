<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un usuario administrador
        User::create([
            'nombre' => 'Administrador',
            'email' => 'admin@chasis.com',
            'password' => Hash::make('admin123'),
            'rol' => 'admin',
            'activo' => true,
        ]);

        // Crear un usuario regular
        User::create([
            'nombre' => 'Usuario Prueba',
            'email' => 'usuario@chasis.com',
            'password' => Hash::make('usuario123'),
            'rol' => 'usuario',
            'activo' => true,
        ]);

        // Crear usuarios adicionales usando el factory
        User::factory(5)->create();
    }
}
