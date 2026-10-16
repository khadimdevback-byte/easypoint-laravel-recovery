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
        Schema::create('repartitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rotation_id')->constrained('rotations')->onDelete('cascade');
            $table->enum('periode', ['matin', 'apres-midi', 'nuit']);
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->integer('nombre_jours')->nullable(); // Durée de cette période dans le cycle
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repartitions');
    }
};
