<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Attention : cette requête est spécifique à MySQL
        DB::statement("ALTER TABLE attendances MODIFY type ENUM('arrivee', 'pause', 'reprise','depart')");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            
        DB::statement("ALTER TABLE attendances MODIFY type ENUM('arrivee', 'depart')");
        });
    }
};
