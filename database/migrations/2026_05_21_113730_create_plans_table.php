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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // FREE, STARTER, etc.
            $table->string('slug')->unique(); // free, starter, etc.
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('currency', 3)->default('XOF');
            $table->integer('max_employees')->default(0); // Limite d'employés (3, 10, 30, 80, -1 pour illimité)
            $table->string('dashboard_type')->default('Basique'); // Basique, Avancé, Complet
            $table->string('history_duration')->default('7 jours'); // 7 jours, 1 mois, etc.
            $table->string('support_type')->default('Email'); // Email, WhatsApp, Prioritaire, Dédié
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
