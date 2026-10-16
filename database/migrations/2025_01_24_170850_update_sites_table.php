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
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('break_time');
            $table->dropColumn('resumption_time');
            $table->dropForeign(['controller_id']);
            $table->dropColumn('controller_id');

            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                $table->boolean("{$day}_checked")->default(false);
                $table->time("start_time_{$day}")->nullable();
                $table->time("end_time_{$day}")->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->time('break_time')->nullable();
            $table->time('resumption_time')->nullable();
            $table->foreignId('controller_id')->nullable()->constrained('users')->onDelete('set null');

            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                $table->dropColumn("{$day}_checked");
                $table->dropColumn("start_time_{$day}");
                $table->dropColumn("end_time_{$day}");
            }
        });
    
    }
};
