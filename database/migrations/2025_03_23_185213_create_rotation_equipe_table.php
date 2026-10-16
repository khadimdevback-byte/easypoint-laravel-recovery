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
        Schema::create('rotation_equipe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rotation_id')->constrained('rotations'); // Clé étrangère vers la table 'rotations'
            $table->foreignId('equipe_id')->constrained('equipes'); // Clé étrangère vers la table 'equipes'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rotation_equipe');
    }
};
