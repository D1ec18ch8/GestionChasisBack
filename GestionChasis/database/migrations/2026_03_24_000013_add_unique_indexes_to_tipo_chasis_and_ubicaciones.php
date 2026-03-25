<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tipo_chasis', function (Blueprint $table): void {
            $table->unique('nombre', 'tipo_chasis_nombre_unique');
        });

        Schema::table('ubicaciones', function (Blueprint $table): void {
            $table->unique('codigo', 'ubicaciones_codigo_unique');
            $table->unique('nombre', 'ubicaciones_nombre_unique');
        });

        Schema::table('estados', function (Blueprint $table): void {
            $table->unique('nombre', 'estados_nombre_unique');
        });

        Schema::table('chasis', function (Blueprint $table): void {
            $table->unique('nombre', 'chasis_nombre_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chasis', function (Blueprint $table): void {
            $table->dropUnique('chasis_nombre_unique');
        });

        Schema::table('estados', function (Blueprint $table): void {
            $table->dropUnique('estados_nombre_unique');
        });

        Schema::table('ubicaciones', function (Blueprint $table): void {
            $table->dropUnique('ubicaciones_nombre_unique');
            $table->dropUnique('ubicaciones_codigo_unique');
        });

        Schema::table('tipo_chasis', function (Blueprint $table): void {
            $table->dropUnique('tipo_chasis_nombre_unique');
        });
    }
};
