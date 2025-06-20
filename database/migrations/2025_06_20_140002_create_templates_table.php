<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type', ['acte', 'courrier']);
            $table->string('categorie');
            $table->text('description')->nullable();
            $table->string('fichier_nom');
            $table->string('fichier_path');
            $table->string('fichier_extension', 10);
            $table->bigInteger('fichier_taille')->unsigned();
            $table->integer('nb_telechargements')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
            
            $table->index(['type', 'categorie']);
            $table->index(['actif', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('templates');
    }
};
