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
        Schema::table('operation_types', function (Blueprint $table) {
            // Ajout du champ boolean pour indiquer si c'est un modèle CCQ
            $table->boolean('is_ccq')->default(false)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_types', function (Blueprint $table) {
            $table->dropColumn('is_ccq');
        });
    }
};
