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
        Schema::create('employee_ccq', function (Blueprint $table) {
            $table->id();

            // Lien avec employees
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            // Champs rouges (spécifiques CCQ)
            $table->decimal('ccq', 10, 2)->nullable();                  // Cotisation CCQ
            $table->decimal('aecq', 10, 2)->nullable();                 // AECQ
            $table->decimal('fonds_divers', 10, 2)->nullable();         // Fonds divers
            $table->decimal('equipement_securite', 10, 2)->nullable();  // Équipement sécurité
            $table->decimal('clauses_normatives', 10, 2)->nullable();   // Clauses normatives et autres

            // Totaux calculés (optionnels si tu veux les stocker)
            $table->decimal('total_cout_horaire', 12, 4)->nullable();   // Total coût horaire de la main-d'œuvre
            $table->decimal('cout_annuel_total', 14, 2)->nullable();    // Coût annuel total

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_ccq');
    }
};
