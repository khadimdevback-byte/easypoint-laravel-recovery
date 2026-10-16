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
        Schema::create('site_user', function (Blueprint $table) {
            $table->id(); // Clé primaire (facultatif mais utile pour la gestion)

            // Clé étrangère vers la table `sites`
            $table->foreignId('site_id')
                ->constrained('sites') // Assurez-vous que la table `sites` existe et utilise `id` comme clé primaire
                ->onDelete('cascade');

            // Clé étrangère vers la table `users`
            $table->foreignId('user_id')
                ->constrained('users') // Assurez-vous que la table `users` existe et utilise `id` comme clé primaire
                ->onDelete('cascade');

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_user');
    }
};
