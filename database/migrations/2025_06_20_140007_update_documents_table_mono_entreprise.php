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
        // Mettre à jour la table documents pour l'architecture mono-entreprise
        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                // Supprimer les références aux sections si elles existent
                if (Schema::hasColumn('documents', 'section_id')) {
                    $table->dropColumn('section_id');
                }
                
                // Ajouter la référence au champ d'activité
                if (!Schema::hasColumn('documents', 'champ_activite_id')) {
                    $table->unsignedBigInteger('champ_activite_id')->nullable();
                    $table->foreign('champ_activite_id')->references('id')->on('champs_activites')->onDelete('set null');
                }
                
                // Améliorer les champs existants
                if (!Schema::hasColumn('documents', 'statut')) {
                    $table->enum('statut', ['actif', 'archive', 'supprime'])->default('actif');
                }
                
                if (!Schema::hasColumn('documents', 'mots_cles')) {
                    $table->text('mots_cles')->nullable();
                }
                
                if (!Schema::hasColumn('documents', 'version')) {
                    $table->string('version', 10)->default('1.0');
                }
                
                if (!Schema::hasColumn('documents', 'confidentialite')) {
                    $table->enum('confidentialite', ['public', 'prive', 'confidentiel'])->default('prive');
                }
                
                if (!Schema::hasColumn('documents', 'nb_consultations')) {
                    $table->integer('nb_consultations')->default(0);
                }
                
                if (!Schema::hasColumn('documents', 'derniere_consultation')) {
                    $table->timestamp('derniere_consultation')->nullable();
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
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['champ_activite_id']);
            $table->dropColumn([
                'champ_activite_id', 
                'statut', 
                'mots_cles', 
                'version', 
                'confidentialite', 
                'nb_consultations', 
                'derniere_consultation'
            ]);
        });
    }
};
