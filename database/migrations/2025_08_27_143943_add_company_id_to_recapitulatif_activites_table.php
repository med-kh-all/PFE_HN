<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Ajouter la colonne nullable + FK
        Schema::table('recapitulatifs_activites', function (Blueprint $table) {
            // si ta table s’appelle différemment, ajuste le nom ↑
            $table->foreignId('company_id')
                  ->nullable()                               // temporaire, on va backfiller
                  ->after('operation_type_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->index('company_id');
        });

        // 2) Backfill depuis operation_types
        // (MySQL/MariaDB OK)
        DB::statement('
            UPDATE recapitulatifs_activites r
            JOIN operation_types o ON o.id = r.operation_type_id
            SET r.company_id = o.company_id
        ');

        // 3) Rendre NOT NULL (sans doctrine/dbal)
        DB::statement('
            ALTER TABLE recapitulatifs_activites
            MODIFY company_id BIGINT UNSIGNED NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('recapitulatifs_activites', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id'); // supprime la FK + la colonne
        });
    }
};
