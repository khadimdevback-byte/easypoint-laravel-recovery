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
        Schema::table('missing_departures', function (Blueprint $table) {
            // Ajouter la colonne company_id après la colonne id
            $table->foreignId('company_id')->after('id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('missing_departures', function (Blueprint $table) {
            // Supprimer la clé étrangère puis la colonne
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
