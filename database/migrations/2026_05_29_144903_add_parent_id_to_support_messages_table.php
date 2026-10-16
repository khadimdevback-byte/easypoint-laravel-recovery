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
       Schema::table('support_messages', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('support_messages')->onDelete('cascade');
            $table->string('sender_type')->default('company'); // 'company' ou 'superadmin'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'sender_type']);
        });
    }
};
