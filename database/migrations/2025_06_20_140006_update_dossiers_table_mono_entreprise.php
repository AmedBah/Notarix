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
        // Mettre à jour la table dossiers pour l'architecture mono-entreprise
        if (Schema::hasTable('dossiers')) {
            Schema::table('dossiers', function (Blueprint $table) {
                // Supprimer la référence aux sections si elle existe
                if (Schema::hasColumn('dossiers', 'section_id')) {
                    $table->dropForeign(['section_id']);
                    $table->dropColumn('section_id');
                }
                
                // Ajouter la référence au champ d'activité
                if (!Schema::hasColumn('dossiers', 'champ_activite_id')) {
                    $table->unsignedBigInteger('champ_activite_id')->nullable();
                    $table->foreign('champ_activite_id')->references('id')->on('champs_activites')->onDelete('set null');
                }
                
                // Ajouter des champs supplémentaires
                if (!Schema::hasColumn('dossiers', 'statut')) {
                    $table->enum('statut', ['ouvert', 'en_cours', 'cloture', 'archive'])->default('ouvert');
                }
                
                if (!Schema::hasColumn('dossiers', 'priorite')) {
                    $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale');
                }
                
                if (!Schema::hasColumn('dossiers', 'description')) {
                    $table->text('description')->nullable();
                }
                
                if (!Schema::hasColumn('dossiers', 'date_echeance')) {
                    $table->date('date_echeance')->nullable();
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
        Schema::table('dossiers', function (Blueprint $table) {
            $table->dropForeign(['champ_activite_id']);
            $table->dropColumn(['champ_activite_id', 'statut', 'priorite', 'description', 'date_echeance']);
        });
    }
};
