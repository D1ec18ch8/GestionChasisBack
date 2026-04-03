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
        $defaultPassword = Hash::make('admin123');

        // Crear un usuario administrador
        User::firstOrCreate([
            'email' => 'admin@chasis.com',
        ], [
            'nombre' => 'Administrador',
            'password' => $defaultPassword,
            'rol' => 'admin',
            'activo' => true,
        ]);

        // Crear un usuario regular
        User::firstOrCreate([
            'email' => 'usuario@chasis.com',
        ], [
            'nombre' => 'Usuario Prueba',
            'password' => Hash::make('usuario123'),
            'rol' => 'usuario',
            'activo' => true,
        ]);

        $additionalUsers = [
            ['nombre' => 'Usuario Demo 1', 'email' => 'demo1@chasis.com'],
            ['nombre' => 'Usuario Demo 2', 'email' => 'demo2@chasis.com'],
            ['nombre' => 'Usuario Demo 3', 'email' => 'demo3@chasis.com'],
            ['nombre' => 'Usuario Demo 4', 'email' => 'demo4@chasis.com'],
            ['nombre' => 'Usuario Demo 5', 'email' => 'demo5@chasis.com'],
        ];

        foreach ($additionalUsers as $userData) {
            User::firstOrCreate([
                'email' => $userData['email'],
            ], [
                'nombre' => $userData['nombre'],
                'password' => Hash::make('usuario123'),
                'rol' => 'usuario',
                'activo' => true,
            ]);
        }
    }
}
