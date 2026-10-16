<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rotations', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('systeme', ['journalier', 'hebdomadaire', 'mensuel'])->default('journalier');
            $table->integer('duree_cycle'); // Nombre total de jours dans un cycle
            // $table->enum('unite_duree', ['jours', 'semaines']);
            $table->integer('nombre_jours_travail'); // Jours travaillés dans le cycle
            $table->integer('nombre_jours_repos'); // Jours de repos (calculé dynamiquement)
            $table->integer('nombre_repartitions');
            $table->date('date_debut')->nullable(); // Date de début de la rotation
            $table->enum('statut', ['active', 'inactive'])->default('active');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rotations');
    }
};
