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
        Schema::table('users', function (Blueprint $table) {
            // $table->string('employee_function')->nullable();
            $table->foreignId('employee_function')->nullable()->constrained(('fonctions'))->onDelete('cascade')->onUpdate('cascade');
            $table->string('qualification')->nullable();
            $table->string('employee_quotient')->nullable();
            $table->enum('identity_document_type', ['cni', 'passport', 'driving_licenses'])->nullable();
            $table->string('id_number')->nullable();
            $table->string('reason')->nullable();
            $table->integer('panier')->nullable();
            $table->enum('customer_type', ['physical_person', 'legal_person'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employee_function');
            $table->dropColumn('qualification');
            $table->dropColumn('employee_quotient');
            $table->dropColumn('identity_document_type');
            $table->dropColumn('id_number');
            $table->dropColumn('reason');
            $table->dropColumn('customer_type');
        });
    }
};
