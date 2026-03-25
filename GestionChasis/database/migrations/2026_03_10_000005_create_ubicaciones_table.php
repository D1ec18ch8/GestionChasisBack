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
        Schema::create('ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->nullable();
            $table->string('razon_social');
            $table->string('aduana', 10)->nullable();
            $table->text('direccion')->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::table('chasis', function (Blueprint $table): void {
            $table->foreign('ubicacion_id')->references('id')->on('ubicaciones')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chasis', function (Blueprint $table): void {
            $table->dropForeign(['ubicacion_id']);
        });

        Schema::dropIfExists('ubicaciones');
    }
};
