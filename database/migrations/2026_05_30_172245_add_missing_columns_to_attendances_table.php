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
        Schema::table('attendances', function (Blueprint $table) {
            // Ajouter les colonnes manquantes
            // $table->enum('status', ['present', 'late', 'absent'])->nullable()->after('type');
            // $table->time('expected_arrival')->nullable()->after('status');
            // $table->integer('delay_in_minutes')->nullable()->default(0)->after('expected_arrival');
            // $table->string('source')->nullable()->default('badge')->after('delay_in_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['status', 'expected_arrival', 'delay_in_minutes', 'source']);
        });
    }
};
