<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        DB::table('estados')->updateOrInsert(
            ['slug' => 'optimo'],
            ['nombre' => 'Optimo', 'updated_at' => $now, 'created_at' => $now]
        );

        DB::table('estados')->updateOrInsert(
            ['slug' => 'revision'],
            ['nombre' => 'Revision', 'updated_at' => $now, 'created_at' => $now]
        );

        DB::table('estados')
            ->where('slug', 'operativo')
            ->delete();

        $optimoId = DB::table('estados')->where('slug', 'optimo')->value('id');
        $revisionId = DB::table('estados')->where('slug', 'revision')->value('id');

        if ($optimoId) {
            DB::table('chasis')
                ->whereNull('estado_id')
                ->update(['estado_id' => $optimoId]);
        }

        if ($revisionId) {
            DB::table('chasis')
                ->where(function ($query) {
                    $query->where('averia_patas', true)
                        ->orWhere('averia_luces', true)
                        ->orWhere('averia_manoplas', true)
                        ->orWhere('averia_mangueras', true)
                        ->orWhere('averia_llantas', true);
                })
                ->update(['estado_id' => $revisionId]);
        }

        if ($optimoId) {
            DB::table('chasis')
                ->where(function ($query) {
                    $query->where('averia_patas', false)
                        ->where('averia_luces', false)
                        ->where('averia_manoplas', false)
                        ->where('averia_mangueras', false)
                        ->where('averia_llantas', false);
                })
                ->update(['estado_id' => $optimoId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $now = now();

        DB::table('estados')->updateOrInsert(
            ['slug' => 'operativo'],
            ['nombre' => 'Operativo', 'updated_at' => $now, 'created_at' => $now]
        );

        DB::table('estados')
            ->where('slug', 'optimo')
            ->delete();
    }
};
