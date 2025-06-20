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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('categorie'); // Justice, Administration, Professionnel, Expert, etc.
            $table->string('telephone', 20);
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('favori')->default(false);
            $table->timestamps();
            
            $table->index(['categorie', 'nom']);
            $table->index(['favori', 'nom']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};
