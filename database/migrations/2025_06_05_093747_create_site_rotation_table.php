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
       Schema::create('site_rotation', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            // $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            // $table->foreignId('controller_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Infos générales
            $table->string('name');
            $table->string('address');
            $table->text('description')->nullable();
            $table->string('timezone')->nullable();
            $table->integer('panier')->nullable();

            // Coordonnées GPS
            $table->float('radius');
            $table->float('latitude');
            $table->float('longitude');

            // Pointage
            $table->integer('late_mark_duration')->nullable();
            $table->string('qr_code')->nullable()->unique();
            $table->string('status')->default('active');

            // Heures des périodes (rotatif)
            $table->time('heure_debut_matin')->nullable();
            $table->time('heure_fin_matin')->nullable();

            $table->time('heure_debut_apres_midi')->nullable();
            $table->time('heure_fin_apres_midi')->nullable();

            $table->time('heure_debut_nuit')->nullable();
            $table->time('heure_fin_nuit')->nullable();

            // Optionnel : jours de repos
            $table->json('jours_repos')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_rotation');
    }
};
