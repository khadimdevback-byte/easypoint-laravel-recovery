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
        Schema::create('pointages', function (Blueprint $table) {
            $table->id();
            // Relations
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('equipe_id')->constrained('equipes')->onDelete('cascade');
            $table->foreignId('site_id')->constrained('site_rotation')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('rotation_id')->nullable()->constrained('rotations')->onDelete('set null');
            $table->foreignId('periode_id')->nullable()->constrained('repartitions');

            // Horodatage
            $table->date('date_pointage');
            $table->time('heure_pointage');

            // Type et statut
            $table->enum('type', ['arrivee', 'depart']);
            $table->enum('statut', ['present', 'retard', 'absence', 'repos'])->nullable();

            // Localisation
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('jour_cycle')->nullable();

            // Index
            $table->index(['user_id', 'date_pointage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};
