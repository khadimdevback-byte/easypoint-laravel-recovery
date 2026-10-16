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
        Schema::create('rotation_site', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('site_rotation')->onDelete('cascade');
            $table->foreignId('rotation_id')->constrained('rotations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rotation_site');
    }
};
