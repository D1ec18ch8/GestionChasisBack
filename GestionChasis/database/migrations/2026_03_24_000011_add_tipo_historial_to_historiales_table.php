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
        Schema::table('historiales', function (Blueprint $table): void {
            $table->string('tipo_historial', 30)
                ->default('accion_app')
                ->after('chasis_referencia_id')
                ->index();
        });

        DB::table('historiales')
            ->whereNull('tipo_historial')
            ->orWhere('tipo_historial', '')
            ->update(['tipo_historial' => 'accion_app']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historiales', function (Blueprint $table): void {
            $table->dropIndex(['tipo_historial']);
            $table->dropColumn('tipo_historial');
        });
    }
};
