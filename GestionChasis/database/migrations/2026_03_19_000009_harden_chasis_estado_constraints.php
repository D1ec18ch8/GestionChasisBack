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
        $optimoId = DB::table('estados')->where('slug', 'optimo')->value('id');

        if ($optimoId) {
            DB::table('chasis')
                ->whereNull('estado_id')
                ->update(['estado_id' => $optimoId]);
        }

        Schema::table('chasis', function (Blueprint $table) {
            $table->dropForeign(['estado_id']);
        });

        Schema::table('chasis', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_id')->nullable(false)->change();
        });

        Schema::table('chasis', function (Blueprint $table) {
            $table->foreign('estado_id')->references('id')->on('estados')->restrictOnDelete();
        });

        Schema::table('chasis', function (Blueprint $table) {
            if (Schema::hasColumn('chasis', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chasis', function (Blueprint $table) {
            if (! Schema::hasColumn('chasis', 'estado')) {
                $table->string('estado')->default('optimo')->after('numero');
            }
        });

        Schema::table('chasis', function (Blueprint $table) {
            $table->dropForeign(['estado_id']);
        });

        Schema::table('chasis', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_id')->nullable()->change();
        });

        Schema::table('chasis', function (Blueprint $table) {
            $table->foreign('estado_id')->references('id')->on('estados')->nullOnDelete();
        });
    }
};
