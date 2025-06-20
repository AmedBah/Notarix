<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CleanCdcMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. D'abord créer les tables de référence qui n'existent pas encore
        if (!Schema::hasTable('champs_activites')) {
            Schema::create('champs_activites', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->text('description')->nullable();
                $table->boolean('actif')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('templates')) {
            Schema::create('templates', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->enum('type', ['acte', 'courrier']);
                $table->text('contenu');
                $table->json('champs_variables')->nullable();
                $table->boolean('actif')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->string('prenom');
                $table->string('telephone')->nullable();
                $table->string('email')->nullable();
                $table->text('adresse')->nullable();
                $table->string('fonction')->nullable();
                $table->text('motif')->nullable();
                $table->enum('type_contact', ['professionnel', 'personnel', 'institutionnel'])->default('professionnel');
                $table->enum('visibilite', ['public', 'prive'])->default('public');
                $table->boolean('actif')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('searches_history')) {
            Schema::create('searches_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->json('search_params');
                $table->enum('search_type', ['simple', 'advanced'])->default('simple');
                $table->integer('results_count')->default(0);
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('document_id')->nullable();
                $table->string('action');
                $table->json('details')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->boolean('restaurable')->default(false);
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 2. Ensuite modifier les tables existantes
        Schema::table('users', function (Blueprint $table) {
            // Ajouter les champs CDC seulement s'ils n'existent pas
            if (!Schema::hasColumn('users', 'prenom')) {
                $table->string('prenom')->nullable()->after('nom');
            }
            if (!Schema::hasColumn('users', 'adresse')) {
                $table->text('adresse')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'fonction')) {
                $table->string('fonction')->nullable()->after('adresse');
            }
            if (!Schema::hasColumn('users', 'motif')) {
                $table->text('motif')->nullable()->after('fonction');
            }
            if (!Schema::hasColumn('users', 'type_personne')) {
                $table->enum('type_personne', ['client', 'temoin', 'expert', 'autre'])->default('client')->after('motif');
            }
            if (!Schema::hasColumn('users', 'visibilite')) {
                $table->enum('visibilite', ['public', 'prive'])->default('public')->after('type_personne');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('visibilite');
            }
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('status');
            }
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
            }
        });

        // 3. Modifier la table dossiers si nécessaire
        Schema::table('dossiers', function (Blueprint $table) {
            if (!Schema::hasColumn('dossiers', 'champ_activite_id')) {
                $table->unsignedBigInteger('champ_activite_id')->nullable()->after('user_id');
                $table->foreign('champ_activite_id')->references('id')->on('champs_activites')->onDelete('set null');
            }
        });

        // 4. Modifier la table documents en dernier
        Schema::table('documents', function (Blueprint $table) {
            // Renommer les colonnes existantes si nécessaire
            if (Schema::hasColumn('documents', 'nom') && !Schema::hasColumn('documents', 'nom_fichier')) {
                $table->renameColumn('nom', 'nom_fichier');
            }
            if (Schema::hasColumn('documents', 'chemin') && !Schema::hasColumn('documents', 'path')) {
                $table->renameColumn('chemin', 'path');
            }

            // Ajouter les nouvelles colonnes CDC
            if (!Schema::hasColumn('documents', 'type_document')) {
                $table->enum('type_document', ['acte', 'courrier', 'piece_justificative', 'scan'])->nullable()->after('type');
            }
            if (!Schema::hasColumn('documents', 'template_id')) {
                $table->unsignedBigInteger('template_id')->nullable()->after('type_document');
            }
            if (!Schema::hasColumn('documents', 'champ_activite_id')) {
                $table->unsignedBigInteger('champ_activite_id')->nullable()->after('template_id');
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
                $table->enum('statut', ['brouillon', 'finalise', 'signe', 'archive'])->default('brouillon')->after('champs_personnalises');
            }
            if (!Schema::hasColumn('documents', 'archive_status')) {
                $table->enum('archive_status', ['manuel', 'auto', 'notification'])->default('manuel')->after('statut');
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
        });

        // 5. Ajouter les clés étrangères à la fin
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'template_id')) {
                return; // Skip si la colonne n'existe pas
            }
            
            // Vérifier si les foreign keys n'existent pas déjà
            try {
                $table->foreign('template_id')->references('id')->on('templates')->onDelete('set null');
            } catch (\Exception $e) {
                // Foreign key existe déjà
            }
            
            try {
                $table->foreign('champ_activite_id')->references('id')->on('champs_activites')->onDelete('set null');
            } catch (\Exception $e) {
                // Foreign key existe déjà
            }
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            if (Schema::hasColumn('activity_logs', 'document_id')) {
                try {
                    $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key existe déjà
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
        // Supprimer dans l'ordre inverse
        Schema::dropIfExists('searches_history');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('templates');
        Schema::dropIfExists('champs_activites');
        
        // Nettoyer les colonnes ajoutées aux tables existantes
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'type_document', 'template_id', 'champ_activite_id', 'contenu_indexe',
                'parties', 'champs_personnalises', 'statut', 'archive_status',
                'scan_available', 'nb_consultations', 'derniere_consultation',
                'restaurable', 'classement_auto'
            ]);
        });
        
        Schema::table('dossiers', function (Blueprint $table) {
            $table->dropColumn(['champ_activite_id']);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'prenom', 'adresse', 'fonction', 'motif', 'type_personne',
                'visibilite', 'status', 'two_factor_enabled', 'two_factor_secret'
            ]);
        });
    }
}
