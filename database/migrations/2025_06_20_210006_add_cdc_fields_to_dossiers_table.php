<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCdcFieldsToDossiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */    public function up()
    {
        Schema::table('dossiers', function (Blueprint $table) {
            $table->unsignedBigInteger('champ_activite_id')->nullable()->after('user_id');
            
            $table->foreign('champ_activite_id')->references('id')->on('champs_activites')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */    public function down()
    {
        Schema::table('dossiers', function (Blueprint $table) {
            $table->dropForeign(['champ_activite_id']);
            $table->dropColumn(['champ_activite_id']);
        });
    }
}
