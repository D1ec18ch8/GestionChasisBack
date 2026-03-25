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
        Schema::create('historiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chasis_id')->nullable()->constrained('chasis')->nullOnDelete();
            $table->unsignedBigInteger('chasis_referencia_id')->nullable()->index();
            $table->string('accion', 30);
            $table->string('descripcion', 255);
            $table->json('detalle')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historiales');
    }
};
