<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE companies DROP CONSTRAINT IF EXISTS companies_status_check');
            DB::statement("ALTER TABLE companies ALTER COLUMN status SET DEFAULT 'pending'");
            DB::statement("ALTER TABLE companies ADD CONSTRAINT companies_status_check CHECK (status IN ('active', 'inactive', 'pending'))");

            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::table('companies')->where('status', 'pending')->update(['status' => 'active']);
            DB::statement('ALTER TABLE companies DROP CONSTRAINT IF EXISTS companies_status_check');
            DB::statement("ALTER TABLE companies ALTER COLUMN status SET DEFAULT 'active'");
            DB::statement("ALTER TABLE companies ADD CONSTRAINT companies_status_check CHECK (status IN ('active', 'inactive'))");

            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active')->change();
        });
    }
};
