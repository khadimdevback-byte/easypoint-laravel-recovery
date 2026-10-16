<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('contact_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('user_id')->nullable(); // admin qui a vu
            $table->string('type'); // new_contact, status_changed, etc
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->index(['is_read', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_notifications');
    }
}
