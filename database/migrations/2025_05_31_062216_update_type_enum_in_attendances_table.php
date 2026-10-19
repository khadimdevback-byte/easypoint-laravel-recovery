<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE attendances DROP CONSTRAINT IF EXISTS attendances_type_check');
            DB::statement("ALTER TABLE attendances ADD CONSTRAINT attendances_type_check CHECK (type IN ('arrivee', 'pause', 'reprise', 'depart'))");

            return;
        }

        DB::statement("ALTER TABLE attendances MODIFY type ENUM('arrivee', 'pause', 'reprise', 'depart')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::table('attendances')->where('type', 'pause')->update(['type' => 'arrivee']);
            DB::table('attendances')->where('type', 'reprise')->update(['type' => 'depart']);
            DB::statement('ALTER TABLE attendances DROP CONSTRAINT IF EXISTS attendances_type_check');
            DB::statement("ALTER TABLE attendances ADD CONSTRAINT attendances_type_check CHECK (type IN ('arrivee', 'depart'))");

            return;
        }

        DB::statement("ALTER TABLE attendances MODIFY type ENUM('arrivee', 'depart')");
    }
};
