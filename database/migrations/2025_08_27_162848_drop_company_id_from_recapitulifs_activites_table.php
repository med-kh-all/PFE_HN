<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recapitulatifs_activites', function (Blueprint $table) {
            // Si la colonne est en contrainte FK, il faut d'abord la supprimer
            // Méthode “tout-en-un” (Laravel >= 8.36) :
            if (Schema::hasColumn('recapitulatifs_activites', 'company_id')) {
                // Essaie d'abord de drop la contrainte si elle existe
                try {
                    $table->dropForeign('recapitulatifs_activites_company_id_foreign');
                } catch (\Throwable $e) {
                    // ignore si la contrainte n'existe pas
                }

                // Puis supprime la colonne
                $table->dropColumn('company_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recapitulatifs_activites', function (Blueprint $table) {
            // Remet la colonne si on rollback
            if (!Schema::hasColumn('recapitulatifs_activites', 'company_id')) {
                $table->foreignId('company_id')
                      ->nullable()
                      ->constrained('companies')
                      ->nullOnDelete();
            }
        });
    }
};
