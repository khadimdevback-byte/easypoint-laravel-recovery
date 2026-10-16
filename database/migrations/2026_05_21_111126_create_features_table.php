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
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom lisible : "Gestion des employés" [cite: 2]
            $table->string('slug')->unique(); // Identifiant unique pour le code : "employees_management" [cite: 2]
            $table->string('icon')->nullable(); // Icône FontAwesome ou Lucide : "fa-users" [cite: 2]
            $table->string('route')->nullable(); // Nom de la route Laravel : "employees.index" [cite: 2]
            $table->boolean('is_active')->default(true); // Permet de désactiver une fonctionnalité globalement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
