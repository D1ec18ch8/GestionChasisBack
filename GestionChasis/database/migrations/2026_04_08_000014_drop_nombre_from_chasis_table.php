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
        if (! Schema::hasColumn('chasis', 'placa')) {
            return;
        }

        $placasDuplicadas = DB::table('chasis')
            ->select('placa')
            ->whereNotNull('placa')
            ->where('placa', '!=', '')
            ->groupBy('placa')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('placa')
            ->all();

        if ($placasDuplicadas !== []) {
            return;
        }

        if ($this->indexExists('chasis', 'chasis_placa_unique')) {
            return;
        }

        Schema::table('chasis', function (Blueprint $table): void {
            $table->unique('placa', 'chasis_placa_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! $this->indexExists('chasis', 'chasis_placa_unique')) {
            return;
        }

        Schema::table('chasis', function (Blueprint $table): void {
            $table->dropUnique('chasis_placa_unique');
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }
};
