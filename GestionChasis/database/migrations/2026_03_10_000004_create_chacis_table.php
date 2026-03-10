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
        Schema::create('chacis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_chacis_id')->constrained('tipo_chacis')->cascadeOnDelete();
            $table->boolean('revision')->default(false);
            $table->unsignedInteger('patas')->default(0);
            $table->unsignedInteger('luces')->default(0);
            $table->unsignedInteger('mangueras')->default(0);
            $table->unsignedInteger('llantas')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chacis');
    }
};
