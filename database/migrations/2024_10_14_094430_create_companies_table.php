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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('compagny_name');
            $table->string('n_siret')->nullable();
            $table->string('n_siren')->nullable();
            $table->string('iban')->nullable();
            $table->string('compagny_email')->unique();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('timezone')->nullable();
            $table->string('address')->nullable();
            $table->string('password');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('logo')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
