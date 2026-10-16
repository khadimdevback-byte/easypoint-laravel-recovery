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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('controller_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('address');
            $table->text('description')->nullable();
            $table->string('timezone')->nullable();
            $table->integer('panier')->nullable();
            $table->float('radius');
            $table->float('latitude');
            $table->float('longitude');
            $table->time('start_time')->nullable();
            $table->time('break_time')->nullable();
            $table->time('resumption_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('late_mark_duration')->nullable();
            $table->string('qr_code')->unique();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
