<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_ccq', function (Blueprint $table) {
            // On les met juste après employee_id -> donc avant 'ccq'
            $table->decimal('avantages_sociaux', 10, 2)->nullable()->after('employee_id');
            $table->decimal('taxes_assurance', 10, 2)->nullable()->after('avantages_sociaux');
        });
    }

    public function down(): void
    {
        Schema::table('employee_ccq', function (Blueprint $table) {
            $table->dropColumn(['avantages_sociaux', 'taxes_assurance']);
        });
    }
};
