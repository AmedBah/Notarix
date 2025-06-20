<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDocumentsTableForCdc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Renommer les colonnes existantes si nécessaire
            if (Schema::hasColumn('documents', 'nom') && !Schema::hasColumn('documents', 'nom_fichier')) {
                $table->renameColumn('nom', 'nom_fichier');
            }
            
            if (Schema::hasColumn('documents', 'chemin') && !Schema::hasColumn('documents', 'path')) {
                $table->renameColumn('chemin', 'path');
            }
            
            if (Schema::hasColumn('documents', 'taille') && !Schema::hasColumn('documents', 'taille_fichier')) {
                $table->renameColumn('taille', 'taille_fichier');
                $table->integer('taille_fichier')->change(); // Changer en integer
            }
            
            if (Schema::hasColumn('documents', 'type') && !Schema::hasColumn('documents', 'type_fichier')) {
                $table->renameColumn('type', 'type_fichier');
            }
            
            // Ajouter les nouvelles colonnes requises par le CDC
            if (!Schema::hasColumn('documents', 'type_document')) {
                $table->enum('type_document', ['acte', 'courrier', 'piece_justificative', 'scan'])
                      ->after('type_fichier');
            }
            
            if (!Schema::hasColumn('documents', 'template_id')) {
                $table->unsignedBigInteger('template_id')->nullable()->after('type_document');
                $table->foreign('template_id')->references('id')->on('templates');
            }
            
            if (!Schema::hasColumn('documents', 'champ_activite_id')) {
                $table->unsignedBigInteger('champ_activite_id')->nullable()->after('template_id');
                $table->foreign('champ_activite_id')->references('id')->on('champs_activites');
            }
            
            if (!Schema::hasColumn('documents', 'contenu_indexe')) {
                $table->text('contenu_indexe')->nullable()->after('champ_activite_id');
            }
            
            if (!Schema::hasColumn('documents', 'parties')) {
                $table->json('parties')->nullable()->after('contenu_indexe');
            }
            
            if (!Schema::hasColumn('documents', 'champs_personnalises')) {
                $table->json('champs_personnalises')->nullable()->after('parties');
            }
            
            if (!Schema::hasColumn('documents', 'statut')) {
                $table->enum('statut', ['brouillon', 'finalise', 'signe', 'archive'])
                      ->default('brouillon')
                      ->after('champs_personnalises');
            }
            
            if (!Schema::hasColumn('documents', 'archive_status')) {
                $table->enum('archive_status', ['manuel', 'auto', 'notification'])
                      ->default('manuel')
                      ->after('statut');
            }
            
            if (!Schema::hasColumn('documents', 'scan_available')) {
                $table->boolean('scan_available')->default(false)->after('archive_status');
            }
            
            if (!Schema::hasColumn('documents', 'nb_consultations')) {
                $table->integer('nb_consultations')->default(0)->after('scan_available');
            }
            
            if (!Schema::hasColumn('documents', 'derniere_consultation')) {
                $table->timestamp('derniere_consultation')->nullable()->after('nb_consultations');
            }
            
            if (!Schema::hasColumn('documents', 'restaurable')) {
                $table->boolean('restaurable')->default(true)->after('derniere_consultation');
            }
            
            if (!Schema::hasColumn('documents', 'classement_auto')) {
                $table->json('classement_auto')->nullable()->after('restaurable');
            }
            
            // Supprimer les colonnes obsolètes
            $obsoleteColumns = [
                'section_id',
                'entreprise_id', 
                'logo_path',
                'content',
                'is_archived',
                'archived_at',
                'archive_path',
                'needs_update',
                'status',
                'client_name',
                'dossier_number'
            ];
            
            foreach ($obsoleteColumns as $column) {
                if (Schema::hasColumn('documents', $column)) {
                    // Supprimer les clés étrangères d'abord si elles existent
                    if (in_array($column, ['section_id', 'entreprise_id'])) {
                        try {
                            $table->dropForeign(['section_id']);
                        } catch (\Exception $e) {
                            // Ignorer si la clé étrangère n'existe pas
                        }
                        try {
                            $table->dropForeign(['entreprise_id']);
                        } catch (\Exception $e) {
                            // Ignorer si la clé étrangère n'existe pas
                        }
                    }
                    $table->dropColumn($column);
                }
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
        Schema::table('documents', function (Blueprint $table) {
            // Retour en arrière des changements
            $table->dropForeign(['template_id']);
            $table->dropForeign(['champ_activite_id']);
            
            $table->dropColumn([
                'type_document',
                'template_id',
                'champ_activite_id',
                'contenu_indexe',
                'parties',
                'champs_personnalises',
                'statut',
                'archive_status',
                'scan_available',
                'nb_consultations',
                'derniere_consultation',
                'restaurable',
                'classement_auto'
            ]);
        });
    }
}
