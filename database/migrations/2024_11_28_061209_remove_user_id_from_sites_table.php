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
        Schema::table('sites', function (Blueprint $table) {
            // Suppression de la contrainte de clé étrangère 'sites_user_id_foreign'
            $table->dropForeign('sites_user_id_foreign');
            
            // Suppression de la colonne 'user_id'
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            // Réajout de la colonne 'user_id' avec les contraintes appropriées
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }
};
