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
        // Ensure duplicate non-empty plates do not block unique index creation.
        DB::statement("
            UPDATE chasis c
            JOIN (
                SELECT id
                FROM (
                    SELECT id,
                           ROW_NUMBER() OVER (PARTITION BY placa ORDER BY id) AS rn
                    FROM chasis
                    WHERE placa IS NOT NULL AND placa <> ''
                ) ranked
                WHERE ranked.rn > 1
            ) dup ON dup.id = c.id
            SET c.placa = CONCAT(c.placa, '-DUP-', c.id)
        ");

        if (Schema::hasColumn('chasis', 'categoria')) {
            Schema::table('chasis', function (Blueprint $table): void {
                $table->dropColumn('categoria');
            });
        }

        if (! $this->indexExists('chasis', 'chasis_placa_unique')) {
            Schema::table('chasis', function (Blueprint $table): void {
                $table->unique('placa', 'chasis_placa_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->indexExists('chasis', 'chasis_placa_unique')) {
            Schema::table('chasis', function (Blueprint $table): void {
                $table->dropUnique('chasis_placa_unique');
            });
        }

        if (! Schema::hasColumn('chasis', 'categoria')) {
            Schema::table('chasis', function (Blueprint $table): void {
                $table->string('categoria')->nullable()->after('nombre');
            });
        }
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
