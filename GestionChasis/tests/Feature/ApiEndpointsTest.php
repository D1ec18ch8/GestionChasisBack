<?php

namespace Tests\Feature;

use App\Models\Chasis;
use App\Models\Estado;
use App\Models\TipoChasis;
use App\Models\Ubicacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_tipo_chasis_validation_and_not_found_endpoints(): void
    {
        $invalidStore = $this->postJson('/api/tipos-chasis', []);
        $invalidStore->assertUnprocessable()->assertJsonValidationErrors(['nombre']);

        $notFoundShow = $this->getJson('/api/tipos-chasis/99999');
        $notFoundShow->assertNotFound();

        $notFoundUpdate = $this->putJson('/api/tipos-chasis/99999', [
            'nombre' => 'No existe',
        ]);
        $notFoundUpdate->assertNotFound();

        $notFoundDelete = $this->deleteJson('/api/tipos-chasis/99999');
        $notFoundDelete->assertNotFound();
    }

    public function test_tipo_chasis_nombre_must_be_unique_on_create_and_update(): void
    {
        $tipoA = TipoChasis::create(['nombre' => 'Duplicado']);
        $tipoB = TipoChasis::create(['nombre' => 'Otro']);

        $duplicatedStore = $this->postJson('/api/tipos-chasis', [
            'nombre' => 'Duplicado',
        ]);

        $duplicatedStore
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre'])
            ->assertJsonPath('errors.nombre.0', 'Ya existe un tipo de chasis con ese nombre.');

        $duplicatedUpdate = $this->putJson("/api/tipos-chasis/{$tipoB->id}", [
            'nombre' => $tipoA->nombre,
        ]);

        $duplicatedUpdate
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre'])
            ->assertJsonPath('errors.nombre.0', 'Ya existe un tipo de chasis con ese nombre.');
    }

    public function test_ubicaciones_validation_and_not_found_endpoints(): void
    {
        $invalidStore = $this->postJson('/api/ubicaciones', [
            'email' => 'correo-no-valido',
        ]);

        $invalidStore
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre', 'razon_social', 'email']);

        $notFoundShow = $this->getJson('/api/ubicaciones/99999');
        $notFoundShow->assertNotFound();

        $notFoundUpdate = $this->putJson('/api/ubicaciones/99999', [
            'nombre' => 'No existe',
            'razon_social' => 'No existe',
        ]);
        $notFoundUpdate->assertNotFound();

        $notFoundDelete = $this->deleteJson('/api/ubicaciones/99999');
        $notFoundDelete->assertNotFound();
    }

    public function test_ubicacion_codigo_must_be_unique_on_create_and_update(): void
    {
        $ubicacionA = Ubicacion::create([
            'codigo' => 'UB-UNIQUE',
            'razon_social' => 'Empresa A',
            'nombre' => 'Patio A',
        ]);

        $ubicacionB = Ubicacion::create([
            'codigo' => 'UB-OTHER',
            'razon_social' => 'Empresa B',
            'nombre' => 'Patio B',
        ]);

        $duplicatedStore = $this->postJson('/api/ubicaciones', [
            'codigo' => 'UB-UNIQUE',
            'razon_social' => 'Empresa C',
            'nombre' => 'Patio C',
        ]);

        $duplicatedStore
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['codigo'])
            ->assertJsonPath('errors.codigo.0', 'Ya existe una ubicacion con ese codigo.');

        $duplicatedUpdate = $this->putJson("/api/ubicaciones/{$ubicacionB->id}", [
            'codigo' => $ubicacionA->codigo,
            'razon_social' => 'Empresa B',
            'nombre' => 'Patio B',
        ]);

        $duplicatedUpdate
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['codigo'])
            ->assertJsonPath('errors.codigo.0', 'Ya existe una ubicacion con ese codigo.');
    }

    public function test_ubicacion_nombre_must_be_unique_on_create_and_update(): void
    {
        $ubicacionA = Ubicacion::create([
            'codigo' => 'UB-NOMBRE-1',
            'razon_social' => 'Empresa A',
            'nombre' => 'Patio Duplicado',
        ]);

        $ubicacionB = Ubicacion::create([
            'codigo' => 'UB-NOMBRE-2',
            'razon_social' => 'Empresa B',
            'nombre' => 'Patio Unico',
        ]);

        $duplicatedStore = $this->postJson('/api/ubicaciones', [
            'codigo' => 'UB-NOMBRE-3',
            'razon_social' => 'Empresa C',
            'nombre' => 'Patio Duplicado',
        ]);

        $duplicatedStore
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre'])
            ->assertJsonPath('errors.nombre.0', 'Ya existe una ubicacion con ese nombre.');

        $duplicatedUpdate = $this->putJson("/api/ubicaciones/{$ubicacionB->id}", [
            'codigo' => 'UB-NOMBRE-2',
            'razon_social' => 'Empresa B',
            'nombre' => $ubicacionA->nombre,
        ]);

        $duplicatedUpdate
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre'])
            ->assertJsonPath('errors.nombre.0', 'Ya existe una ubicacion con ese nombre.');
    }

    public function test_estados_validation_and_not_found_endpoints(): void
    {
        $invalidStore = $this->postJson('/api/estados', [
            'nombre' => '',
            'slug' => 'slug con espacios',
        ]);

        $invalidStore
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre', 'slug']);

        $notFoundShow = $this->getJson('/api/estados/99999');
        $notFoundShow->assertNotFound();

        $notFoundUpdate = $this->putJson('/api/estados/99999', [
            'nombre' => 'No existe',
            'slug' => 'no-existe',
        ]);
        $notFoundUpdate->assertNotFound();

        $notFoundDelete = $this->deleteJson('/api/estados/99999');
        $notFoundDelete->assertNotFound();
    }

    public function test_estados_base_are_protected_and_in_use_cannot_be_deleted(): void
    {
        $optimo = Estado::query()->where('slug', 'optimo')->firstOrFail();
        $revision = Estado::query()->where('slug', 'revision')->firstOrFail();

        $updateBaseResponse = $this->putJson("/api/estados/{$optimo->id}", [
            'nombre' => 'Optimo Editado',
        ]);
        $updateBaseResponse->assertStatus(409);

        $deleteBaseResponse = $this->deleteJson("/api/estados/{$revision->id}");
        $deleteBaseResponse->assertStatus(409);

        $tipo = TipoChasis::create(['nombre' => 'Tipo InUse']);
        $ubicacion = Ubicacion::create([
            'codigo' => 'UB-INUSE',
            'razon_social' => 'Empresa InUse',
            'nombre' => 'Patio InUse',
        ]);

        $estadoCustom = Estado::create([
            'nombre' => 'Temporal',
            'slug' => 'temporal',
        ]);

        Chasis::create([
            'tipo_chasis_id' => $tipo->id,
            'ubicacion_id' => $ubicacion->id,
            'estado_id' => $estadoCustom->id,
            'placa' => 'TEMP-3333',
            'numero' => 3333,
            'averia_patas' => false,
            'averia_luces' => false,
            'averia_manoplas' => false,
            'averia_mangueras' => false,
            'averia_llantas' => false,
        ]);

        $deleteInUseResponse = $this->deleteJson("/api/estados/{$estadoCustom->id}");
        $deleteInUseResponse->assertStatus(409);
    }

    public function test_estado_nombre_must_be_unique_with_custom_message(): void
    {
        $duplicatedStore = $this->postJson('/api/estados', [
            'nombre' => 'Optimo',
            'slug' => 'optimo-duplicado',
        ]);

        $duplicatedStore
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre'])
            ->assertJsonPath('errors.nombre.0', 'Ya existe un estado con ese nombre.');
    }

    public function test_chasis_validation_and_not_found_endpoints(): void
    {
        $tipo = TipoChasis::create(['nombre' => 'Tipo Base']);

        $invalidStore = $this->postJson('/api/chasis', [
            'tipo_chasis_id' => $tipo->id,
            'estado_id' => 1,
            'estado' => 'revision',
        ]);

        $invalidStore
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['placa', 'estado', 'estado_id']);

        $notFoundShow = $this->getJson('/api/chasis/99999');
        $notFoundShow->assertNotFound();

        $notFoundUpdate = $this->putJson('/api/chasis/99999', ['placa' => 'NO-EXISTE']);
        $notFoundUpdate->assertNotFound();

        $notFoundDelete = $this->deleteJson('/api/chasis/99999');
        $notFoundDelete->assertNotFound();
    }

    public function test_chasis_placa_must_be_unique_with_custom_message(): void
    {
        $tipo = TipoChasis::create(['nombre' => 'Tipo Chasis Duplicate']);
        $ubicacion = Ubicacion::create([
            'codigo' => 'UB-CHD-1',
            'razon_social' => 'Empresa Chasis Duplicate',
            'nombre' => 'Patio Chasis Duplicate',
        ]);

        $this->postJson('/api/chasis', [
            'tipo_chasis_id' => $tipo->id,
            'ubicacion_id' => $ubicacion->id,
            'placa' => 'CH-UNICO-1',
            'numero' => 7788,
            'averia_luces' => false,
        ])->assertCreated();

        $duplicatedStore = $this->postJson('/api/chasis', [
            'tipo_chasis_id' => $tipo->id,
            'ubicacion_id' => $ubicacion->id,
            'placa' => 'CH-UNICO-1',
            'numero' => 7799,
            'averia_luces' => false,
        ]);

        $duplicatedStore
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['placa'])
            ->assertJsonPath('errors.placa.0', 'Ya existe un chasis con esa placa.');
    }

    public function test_tipo_chasis_crud_endpoints(): void
    {
        $indexResponse = $this->getJson('/api/tipos-chasis');
        $indexResponse->assertOk();

        $storeResponse = $this->postJson('/api/tipos-chasis', [
            'nombre' => 'Plataforma',
        ]);
        $storeResponse
            ->assertCreated()
            ->assertJsonPath('message', 'Tipo de chasis creado exitosamente.');
        $tipoId = $storeResponse->json('id');

        $showResponse = $this->getJson("/api/tipos-chasis/{$tipoId}");
        $showResponse->assertOk()->assertJsonPath('id', $tipoId);

        $updateResponse = $this->putJson("/api/tipos-chasis/{$tipoId}", [
            'nombre' => 'Plataforma Actualizada',
        ]);
        $updateResponse
            ->assertOk()
            ->assertJsonPath('message', 'Tipo de chasis actualizado exitosamente.')
            ->assertJsonPath('nombre', 'Plataforma Actualizada');

        $deleteResponse = $this->deleteJson("/api/tipos-chasis/{$tipoId}");
        $deleteResponse->assertNoContent();
    }

    public function test_ubicaciones_crud_endpoints(): void
    {
        $indexResponse = $this->getJson('/api/ubicaciones');
        $indexResponse->assertOk();

        $storeResponse = $this->postJson('/api/ubicaciones', [
            'codigo' => 'UB-001',
            'razon_social' => 'Empresa Demo',
            'aduana' => 'ADU01',
            'direccion' => 'Zona Industrial 123',
            'telefono' => '555-1234',
            'fax' => '555-9999',
            'email' => 'demo@empresa.com',
            'nombre' => 'Patio Norte',
        ]);
        $storeResponse
            ->assertCreated()
            ->assertJsonPath('message', 'Ubicacion creada exitosamente.');
        $ubicacionId = $storeResponse->json('id');

        $showResponse = $this->getJson("/api/ubicaciones/{$ubicacionId}");
        $showResponse->assertOk()->assertJsonPath('id', $ubicacionId);

        $updateResponse = $this->putJson("/api/ubicaciones/{$ubicacionId}", [
            'nombre' => 'Patio Norte 2',
            'razon_social' => 'Empresa Demo 2',
        ]);
        $updateResponse
            ->assertOk()
            ->assertJsonPath('message', 'Ubicacion actualizada exitosamente.')
            ->assertJsonPath('nombre', 'Patio Norte 2');

        $deleteResponse = $this->deleteJson("/api/ubicaciones/{$ubicacionId}");
        $deleteResponse->assertNoContent();
    }

    public function test_estados_crud_endpoints(): void
    {
        $indexResponse = $this->getJson('/api/estados');
        $indexResponse->assertOk();

        $storeResponse = $this->postJson('/api/estados', [
            'nombre' => 'Mantenimiento',
            'slug' => 'mantenimiento',
        ]);
        $storeResponse
            ->assertCreated()
            ->assertJsonPath('message', 'Estado creado exitosamente.');
        $estadoId = $storeResponse->json('id');

        $showResponse = $this->getJson("/api/estados/{$estadoId}");
        $showResponse->assertOk()->assertJsonPath('id', $estadoId);

        $updateResponse = $this->putJson("/api/estados/{$estadoId}", [
            'nombre' => 'Mantenimiento Programado',
            'slug' => 'mantenimiento-programado',
        ]);
        $updateResponse
            ->assertOk()
            ->assertJsonPath('message', 'Estado actualizado exitosamente.')
            ->assertJsonPath('slug', 'mantenimiento-programado');

        $deleteResponse = $this->deleteJson("/api/estados/{$estadoId}");
        $deleteResponse->assertNoContent();
    }

    public function test_chasis_crud_endpoints_and_estado_logic(): void
    {
        $tipo = TipoChasis::create(['nombre' => 'Plataforma']);
        $ubicacion = Ubicacion::create([
            'codigo' => 'UB-002',
            'razon_social' => 'Empresa Chasis',
            'nombre' => 'Patio Sur',
        ]);

        $indexResponse = $this->getJson('/api/chasis');
        $indexResponse->assertOk();

        $storeResponse = $this->postJson('/api/chasis', [
            'tipo_chasis_id' => $tipo->id,
            'ubicacion_id' => $ubicacion->id,
            'placa' => 'ABC-123',
            'numero' => 1001,
            'averia_patas' => false,
            'averia_luces' => true,
            'averia_manoplas' => false,
            'averia_mangueras' => false,
            'averia_llantas' => false,
        ]);

        $storeResponse
            ->assertCreated()
            ->assertJsonPath('message', 'Chasis creado exitosamente.');
        $chasisId = $storeResponse->json('id');
        $revisionId = Estado::query()->where('slug', 'revision')->value('id');

        $storeResponse
            ->assertJsonPath('estado_id', $revisionId)
            ->assertJsonPath('estado_actual', 'revision')
            ->assertJsonPath('equipamientos_en_mal_estado.0', 'luces');

        $showResponse = $this->getJson("/api/chasis/{$chasisId}");
        $showResponse->assertOk()->assertJsonPath('id', $chasisId);

        $updateResponse = $this->putJson("/api/chasis/{$chasisId}", [
            'averia_luces' => false,
            'averia_llantas' => false,
        ]);

        $optimoId = Estado::query()->where('slug', 'optimo')->value('id');

        $updateResponse
            ->assertOk()
            ->assertJsonPath('message', 'Chasis actualizado exitosamente.')
            ->assertJsonPath('estado_id', $optimoId)
            ->assertJsonPath('estado_actual', 'optimo')
            ->assertJsonPath('equipamientos_en_mal_estado', []);

        $deleteResponse = $this->deleteJson("/api/chasis/{$chasisId}");
        $deleteResponse->assertNoContent();

        $this->assertDatabaseMissing('chasis', ['id' => $chasisId]);
    }

    public function test_chasis_index_supports_pagination_and_filters(): void
    {
        $tipo = TipoChasis::create(['nombre' => 'Filtrado Tipo']);
        $ubicacion = Ubicacion::create([
            'codigo' => 'UB-FLT',
            'razon_social' => 'Empresa Filtros',
            'nombre' => 'Patio Filtros',
        ]);

        $this->postJson('/api/chasis', [
            'tipo_chasis_id' => $tipo->id,
            'ubicacion_id' => $ubicacion->id,
            'placa' => 'REV-2001',
            'numero' => 2001,
            'averia_luces' => true,
        ])->assertCreated();

        $this->postJson('/api/chasis', [
            'tipo_chasis_id' => $tipo->id,
            'ubicacion_id' => $ubicacion->id,
            'placa' => 'OPT-2002',
            'numero' => 2002,
            'averia_luces' => false,
        ])->assertCreated();

        $response = $this->getJson('/api/chasis?per_page=1&estado=revision&equipamiento_mal=luces&search=REV-2001');

        $response
            ->assertOk()
            ->assertJsonPath('per_page', 1)
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.estado_actual', 'revision')
            ->assertJsonPath('data.0.equipamientos_en_mal_estado.0', 'luces');
    }

    public function test_historial_module_tracks_chasis_lifecycle_and_exposes_endpoints(): void
    {
        $tipo = TipoChasis::create(['nombre' => 'Tipo Historial']);
        $ubicacion = Ubicacion::create([
            'codigo' => 'UB-HIS',
            'razon_social' => 'Empresa Historial',
            'nombre' => 'Patio Historial',
        ]);
        $ubicacionNueva = Ubicacion::create([
            'codigo' => 'UB-HIS-2',
            'razon_social' => 'Empresa Historial 2',
            'nombre' => 'Patio Historial 2',
        ]);

        $storeResponse = $this->postJson('/api/chasis', [
            'tipo_chasis_id' => $tipo->id,
            'ubicacion_id' => $ubicacion->id,
            'placa' => 'HIS-5001',
            'numero' => 5001,
            'averia_luces' => true,
        ]);

        $storeResponse->assertCreated();
        $chasisId = $storeResponse->json('id');

        $this->putJson("/api/chasis/{$chasisId}", [
            'ubicacion_id' => $ubicacionNueva->id,
            'averia_luces' => false,
        ])->assertOk();

        $this->deleteJson("/api/chasis/{$chasisId}")->assertNoContent();

        $historialAcciones = $this->getJson('/api/historial/acciones?per_page=10');
        $historialAcciones
            ->assertOk()
            ->assertJsonPath('total', 3)
            ->assertJsonPath('data.0.accion', 'eliminacion')
            ->assertJsonPath('data.1.accion', 'actualizacion')
            ->assertJsonPath('data.2.accion', 'creacion');

        $historialMovimientos = $this->getJson('/api/historial/movimientos?per_page=10&placa=HIS-5001');
        $historialMovimientos
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.accion', 'movimiento_ubicacion')
            ->assertJsonPath('data.0.detalle.placa', 'HIS-5001')
            ->assertJsonPath('data.0.detalle.origen', 'Patio Historial')
            ->assertJsonPath('data.0.detalle.destino', 'Patio Historial 2');

        $pdfGeneralResponse = $this->get('/api/historial/movimientos/pdf');
        $pdfGeneralResponse
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $pdfPlacaResponse = $this->get('/api/historial/movimientos/pdf?placa=HIS-5001');
        $pdfPlacaResponse
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
