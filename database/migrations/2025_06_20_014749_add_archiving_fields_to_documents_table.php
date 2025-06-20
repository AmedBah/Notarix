<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchivingFieldsToDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('visibility')->default('public')->after('entreprise_id');
            $table->boolean('is_archived')->default(false)->after('visibility');
            $table->timestamp('archived_at')->nullable()->after('is_archived');
            $table->string('archive_path')->nullable()->after('archived_at');
            $table->boolean('needs_update')->default(false)->after('archive_path'); // notification de mise à jour
            $table->enum('status', ['brouillon', 'en_cours', 'valide', 'archive'])->default('brouillon')->after('needs_update');
            $table->string('client_name')->nullable()->after('status'); // nom du client pour recherche
            $table->string('dossier_number')->nullable()->after('client_name'); // numéro de dossier
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
            $table->dropColumn([
                'visibility',
                'is_archived', 
                'archived_at', 
                'archive_path', 
                'needs_update', 
                'status', 
                'client_name', 
                'dossier_number'
            ]);
        });
    }
}
