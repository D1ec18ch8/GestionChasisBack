<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test registro exitoso.
     */
    public function test_register_success(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'nombre' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'nombre', 'email', 'rol'],
                'token',
            ])
            ->assertJsonPath('message', 'Usuario registrado exitosamente.')
            ->assertJsonPath('user.nombre', 'Nuevo Usuario')
            ->assertJsonPath('user.email', 'nuevo@example.com')
            ->assertJsonPath('user.rol', 'usuario');

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@example.com',
        ]);
    }

    /**
     * Test registro con email duplicado.
     */
    public function test_register_email_already_exists(): void
    {
        User::create([
            'nombre' => 'Usuario Existente',
            'email' => 'existente@example.com',
            'password' => bcrypt('password123'),
            'rol' => 'usuario',
        ]);

        $response = $this->postJson('/api/auth/register', [
            'nombre' => 'Otro Usuario',
            'email' => 'existente@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'Ya existe un usuario con ese correo electrónico.');
    }

    /**
     * Test registro con contraseñas no coincidentes.
     */
    public function test_register_password_mismatch(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'nombre' => 'Usuario Nuevo',
            'email' => 'nuevo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.password.0', 'Las contraseñas no coinciden.');
    }

    /**
     * Test login exitoso.
     */
    public function test_login_success(): void
    {
        $user = User::factory()->create([
            'email' => 'usuario@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'usuario@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'nombre', 'email', 'rol'],
                'token',
            ])
            ->assertJsonPath('message', 'Sesión iniciada exitosamente.')
            ->assertJsonPath('user.email', 'usuario@example.com');
    }

    /**
     * Test login con credenciales inválidas.
     */
    public function test_login_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'usuario@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'usuario@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Las credenciales proporcionadas son incorrectas.');
    }

    /**
     * Test login con usuario inactivo.
     */
    public function test_login_user_inactive(): void
    {
        User::factory()->inactive()->create([
            'email' => 'inactivo@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'inactivo@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('message', 'El usuario no está activo.');
    }

    /**
     * Test obtener usuario autenticado.
     */
    public function test_get_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'nombre', 'email', 'rol', 'activo'],
            ])
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', $user->email);
    }

    /**
     * Test logout exitoso.
     */
    public function test_logout_success(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Sesión cerrada exitosamente.');
    }

    /**
     * Test acceso denegado sin autenticación.
     */
    public function test_access_denied_without_authentication(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    /**
     * Test actualizar perfil.
     */
    public function test_update_profile(): void
    {
        $user = User::factory()->create([
            'nombre' => 'Nombre Antiguo',
            'email' => 'correo@example.com',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/profile', [
                'nombre' => 'Nombre Nuevo',
                'email' => 'nuevo@example.com',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Perfil actualizado exitosamente.')
            ->assertJsonPath('user.nombre', 'Nombre Nuevo')
            ->assertJsonPath('user.email', 'nuevo@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nombre' => 'Nombre Nuevo',
        ]);
    }
}
