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
        Schema::create('offres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poste_id')->constrained('fonctions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade')->onDelete('cascade');
            $table->decimal('taux_horaire')->nullable();
            $table->integer('nombre_personne_recherche');
            $table->string('description_offre');
            $table->enum('demarrage', ['immediat', 'A_partir_de'])->default('A_partir_de');
            $table->date('start_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offres');
    }
};
