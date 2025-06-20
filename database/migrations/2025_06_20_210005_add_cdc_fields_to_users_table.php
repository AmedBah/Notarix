<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCdcFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('prenom')->nullable()->after('nom');
            $table->text('adresse')->nullable()->after('email');
            $table->string('fonction')->nullable()->after('adresse');
            $table->text('motif')->nullable()->after('fonction');
            $table->enum('type_personne', ['client', 'temoin', 'expert', 'autre'])->default('client')->after('motif');
            $table->enum('visibilite', ['public', 'prive'])->default('public')->after('type_personne');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('visibilite');
            $table->string('role')->default('utilisateur')->after('status');
            $table->json('permissions')->nullable()->after('role');
            $table->boolean('two_factor_enabled')->default(false)->after('permissions');
            $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'prenom', 'adresse', 'fonction', 'motif', 'type_personne',
                'visibilite', 'status', 'role', 'permissions',
                'two_factor_enabled', 'two_factor_secret'
            ]);
        });
    }
}
