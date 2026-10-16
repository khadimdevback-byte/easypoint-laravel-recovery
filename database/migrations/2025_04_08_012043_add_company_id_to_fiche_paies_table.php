<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('fiche_paies', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable(); // Assurez-vous que le type est approprié
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade'); // Relation à la table companies
        });
    }

    public function down()
    {
        Schema::table('fiche_paies', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};
