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
        Schema::table('chasis', function (Blueprint $table) {
            $table->foreignId('estado_id')->nullable()->after('numero')->constrained('estados')->nullOnDelete();
            $table->boolean('averia_patas')->default(false)->after('estado_id');
            $table->boolean('averia_luces')->default(false)->after('averia_patas');
            $table->boolean('averia_manoplas')->default(false)->after('averia_luces');
            $table->boolean('averia_mangueras')->default(false)->after('averia_manoplas');
            $table->boolean('averia_llantas')->default(false)->after('averia_mangueras');
        });

        $optimoId = DB::table('estados')->where('slug', 'optimo')->value('id');
        $revisionId = DB::table('estados')->where('slug', 'revision')->value('id');

        if ($optimoId) {
            DB::table('chasis')->update(['estado_id' => $optimoId]);
        }

        if ($revisionId) {
            DB::table('chasis')
                ->whereIn(DB::raw('LOWER(estado)'), ['revision', 'revisión'])
                ->update(['estado_id' => $revisionId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chasis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('estado_id');
            $table->dropColumn([
                'averia_patas',
                'averia_luces',
                'averia_manoplas',
                'averia_mangueras',
                'averia_llantas',
            ]);
        });
    }
};
