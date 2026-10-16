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
        Schema::create('heure_mensuelles', function (Blueprint $table) {
            $table->id();

            // Références
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            // Mois et année
            $table->unsignedTinyInteger('mois');  // 1 à 12
            $table->unsignedSmallInteger('annee'); // ex : 2025

            // Données agrégées
            $table->decimal('total_heures', 6, 2)->default(0); // heures au format 1234.50

            $table->timestamps();

            $table->unique(['user_id', 'mois', 'annee']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heure_mensuelles');
    }
};
