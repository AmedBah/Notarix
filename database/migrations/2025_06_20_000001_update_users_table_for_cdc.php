<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableForCdc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Ajouter les nouveaux champs requis par le CDC
            if (!Schema::hasColumn('users', 'prenom')) {
                $table->string('prenom')->after('nom');
            }
            
            if (!Schema::hasColumn('users', 'fonction')) {
                $table->string('fonction')->nullable()->after('adresse');
            }
            
            if (!Schema::hasColumn('users', 'motif')) {
                $table->text('motif')->nullable()->after('fonction');
            }
            
            if (!Schema::hasColumn('users', 'type_personne')) {
                $table->enum('type_personne', ['client', 'temoin', 'expert', 'autre'])
                      ->default('client')
                      ->after('motif');
            }
            
            if (!Schema::hasColumn('users', 'visibilite')) {
                $table->enum('visibilite', ['public', 'prive'])
                      ->default('public')
                      ->after('type_personne');
            }
            
            if (!Schema::hasColumn('users', 'adresse')) {
                $table->text('adresse')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])
                      ->default('active')
                      ->after('visibilite');
            }
            
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')
                      ->default(false)
                      ->after('status');
            }
            
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->string('two_factor_secret')
                      ->nullable()
                      ->after('two_factor_enabled');
            }
            
            // Supprimer les colonnes obsolètes (seulement si elles existent)
            if (Schema::hasColumn('users', 'entreprise_id')) {
                $table->dropForeign(['entreprise_id']);
                $table->dropColumn('entreprise_id');
            }
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
            // Remettre les colonnes supprimées si nécessaire
            $table->dropColumn([
                'prenom',
                'fonction', 
                'motif',
                'type_personne',
                'visibilite',
                'adresse',
                'status',
                'two_factor_enabled',
                'two_factor_secret'
            ]);
        });
    }
}
