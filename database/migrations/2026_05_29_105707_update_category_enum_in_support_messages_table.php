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
            DB::statement('ALTER TABLE support_messages DROP CONSTRAINT IF EXISTS support_messages_category_check');
            DB::statement("ALTER TABLE support_messages ADD CONSTRAINT support_messages_category_check CHECK (category IN ('question', 'bug', 'probleme', 'facturation', 'renouvellement', 'autre'))");

            return;
        }

        Schema::table('support_messages', function (Blueprint $table) {
            $table->enum('category', ['question', 'bug', 'probleme', 'facturation', 'renouvellement', 'autre'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::table('support_messages')->where('category', 'probleme')->update(['category' => 'bug']);
            DB::statement('ALTER TABLE support_messages DROP CONSTRAINT IF EXISTS support_messages_category_check');
            DB::statement("ALTER TABLE support_messages ADD CONSTRAINT support_messages_category_check CHECK (category IN ('question', 'bug', 'facturation', 'renouvellement', 'autre'))");

            return;
        }

        DB::table('support_messages')->where('category', 'probleme')->update(['category' => 'bug']);

        Schema::table('support_messages', function (Blueprint $table) {
            $table->enum('category', ['question', 'bug', 'facturation', 'renouvellement', 'autre'])->change();
        });
    }
};
