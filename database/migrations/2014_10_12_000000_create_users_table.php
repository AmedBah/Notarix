<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('telephone')->nullable();
            $table->string('email')->unique();
            $table->string('logo_path')->nullable();
            $table->boolean('est_admin')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // Suppression de entreprise_id pour architecture mono-entreprise
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
