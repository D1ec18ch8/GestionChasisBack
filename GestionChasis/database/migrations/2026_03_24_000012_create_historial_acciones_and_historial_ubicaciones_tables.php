<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historial_acciones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chasis_id')->nullable()->constrained('chasis')->nullOnDelete();
            $table->unsignedBigInteger('chasis_referencia_id')->nullable()->index();
            $table->string('accion', 30);
            $table->string('descripcion', 255);
            $table->json('detalle')->nullable();
            $table->timestamps();
        });

        Schema::create('historial_ubicaciones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chasis_id')->nullable()->constrained('chasis')->nullOnDelete();
            $table->unsignedBigInteger('chasis_referencia_id')->nullable()->index();
            $table->string('accion', 30);
            $table->string('descripcion', 255);
            $table->json('detalle')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('historiales')) {
            $tieneTipoHistorial = Schema::hasColumn('historiales', 'tipo_historial');

            $registros = DB::table('historiales')->orderBy('id')->get();

            foreach ($registros as $registro) {
                $esUbicacion = false;
                if ($tieneTipoHistorial && ($registro->tipo_historial ?? null) === 'movimiento') {
                    $esUbicacion = true;
                }

                if (($registro->accion ?? null) === 'movimiento_ubicacion') {
                    $esUbicacion = true;
                }

                $tablaDestino = $esUbicacion ? 'historial_ubicaciones' : 'historial_acciones';

                DB::table($tablaDestino)->insert([
                    'chasis_id' => $registro->chasis_id,
                    'chasis_referencia_id' => $registro->chasis_referencia_id,
                    'accion' => $registro->accion,
                    'descripcion' => $registro->descripcion,
                    'detalle' => $registro->detalle,
                    'created_at' => $registro->created_at,
                    'updated_at' => $registro->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_ubicaciones');
        Schema::dropIfExists('historial_acciones');
    }
};
