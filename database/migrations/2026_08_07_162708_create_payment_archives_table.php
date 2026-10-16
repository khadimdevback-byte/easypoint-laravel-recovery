<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_archives', function (Blueprint $table) {
            $table->id();

            // Informations sur la compagnie (sans contrainte FK)
            $table->foreignId('company_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();

            // Informations sur la souscription
            $table->foreignId('subscription_id')->nullable();
            $table->string('plan_name')->nullable();
            $table->integer('duration_months')->nullable();

            // Informations sur le paiement
            $table->string('fedapay_transaction_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('XOF');
            $table->string('payment_method')->nullable();
            $table->string('status')->default('pending');

            // Données complètes en JSON (backup)
            $table->json('full_payment_data')->nullable();
            $table->json('full_company_data')->nullable();
            $table->json('full_subscription_data')->nullable();

            // Dates
            $table->timestamp('payment_date')->nullable();
            $table->timestamp('archived_at')->nullable(); 
            $table->timestamp('deleted_at')->nullable();

            $table->timestamps();

            // Index pour les recherches
            $table->index(['company_id', 'archived_at']);
            $table->index('fedapay_transaction_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_archives');
    }
};
