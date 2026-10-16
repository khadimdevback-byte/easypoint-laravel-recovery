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
        // Schema::create('fiche_paies', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        //     $table->string('mois'); // exemple : "04" ou "Avril"
        //     $table->integer('annee');
        //     $table->decimal('heures_travaillees', 8, 2)->default(0);
        //     $table->decimal('taux_horaire', 8, 2)->default(0);
        //     $table->decimal('salaire_brut', 10, 2)->default(0);
        //     $table->decimal('primes', 10, 2)->default(0);
        //     $table->decimal('retenues', 10, 2)->default(0);
        //     $table->decimal('cotisations', 10, 2)->default(0);
        //     $table->decimal('avancements', 10, 2)->default(0);
        //     $table->decimal('salaire_net', 10, 2)->default(0);
        //     $table->timestamps();
        // });
        Schema::create('fiche_paies', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        // Période
        $table->string('mois'); // ex: "04" ou "Avril"
        $table->integer('annee');

        // Période exacte
        $table->date('periode_debut')->nullable();
        $table->date('periode_fin')->nullable();

        // Heures et taux
        $table->decimal('heures_travaillees', 8, 2)->default(0);
        $table->decimal('taux_horaire', 8, 2)->default(0);

        // Composants du salaire
        $table->decimal('salaire_brut', 10, 2)->default(0);
        $table->decimal('primes', 10, 2)->default(0);
        $table->decimal('avancements', 10, 2)->default(0); // éventuelles majorations
        $table->decimal('retenues', 10, 2)->default(0);    // avances ou sanctions

        // Cotisations sociales
        $table->decimal('cotisations', 10, 2)->default(0);
        $table->json('cotisations_details')->nullable(); // détails des cotisations (libelle, base, taux, part salariale, part employeur)

        // Impôts et nets
        $table->decimal('net_social', 10, 2)->default(0); // nouveau "montant net social"
        $table->decimal('net_avant_impot', 10, 2)->default(0);
        $table->decimal('impot_source', 10, 2)->default(0);
        $table->decimal('salaire_net', 10, 2)->default(0); // Net à payer après impôt

        // Paiement
        $table->date('date_paiement')->nullable();

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiche_paies');
    }
};
