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
        // Suppression des colonnes liées à l'architecture multi-entreprise
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Supprimer la colonne entreprise_id si elle existe
                if (Schema::hasColumn('users', 'entreprise_id')) {
                    $table->dropForeign(['entreprise_id']);
                    $table->dropColumn('entreprise_id');
                }
                
                // Ajouter/modifier les colonnes pour l'architecture mono-entreprise
                if (!Schema::hasColumn('users', 'role')) {
                    $table->enum('role', ['admin', 'utilisateur'])->default('utilisateur');
                }
                
                if (!Schema::hasColumn('users', 'statut')) {
                    $table->enum('statut', ['actif', 'inactif'])->default('actif');
                }
                
                if (!Schema::hasColumn('users', 'telephone')) {
                    $table->string('telephone', 20)->nullable();
                }
                
                if (!Schema::hasColumn('users', 'adresse')) {
                    $table->text('adresse')->nullable();
                }
                
                // Champs pour l'administration centralisée
                if (!Schema::hasColumn('users', 'derniere_connexion')) {
                    $table->timestamp('derniere_connexion')->nullable();
                }
                
                if (!Schema::hasColumn('users', 'est_admin')) {
                    $table->boolean('est_admin')->default(false);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'statut', 'telephone', 'adresse', 'derniere_connexion', 'est_admin']);
        });
    }
};
