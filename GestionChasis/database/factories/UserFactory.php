<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'rol' => 'usuario',
            'activo' => true,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model should be an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'admin',
        ]);
    }

    /**
     * Indicate that the model should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }
}
