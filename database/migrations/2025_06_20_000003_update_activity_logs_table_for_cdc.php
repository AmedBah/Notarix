<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateActivityLogsTableForCdc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Supprimer les anciennes colonnes si elles existent
            $oldColumns = ['model_type', 'model_id', 'model_name', 'old_values', 'new_values'];
            foreach ($oldColumns as $column) {
                if (Schema::hasColumn('activity_logs', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Ajouter les nouvelles colonnes requises par le CDC
            if (!Schema::hasColumn('activity_logs', 'document_id')) {
                $table->unsignedBigInteger('document_id')->nullable()->after('user_id');
                $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('activity_logs', 'details')) {
                $table->json('details')->nullable()->after('action');
            }
            
            if (!Schema::hasColumn('activity_logs', 'restaurable')) {
                $table->boolean('restaurable')->default(false)->after('user_agent');
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
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['document_id']);
            $table->dropColumn(['document_id', 'details', 'restaurable']);
            
            // Remettre les anciennes colonnes
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_name')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
        });
    }
}
