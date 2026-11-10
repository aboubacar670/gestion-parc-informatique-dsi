<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('intervention_technicien', function (Blueprint $table) {
            $table->foreignId('intervention_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('technicien_id')->after('intervention_id')->constrained('techniciens');
            $table->date('date_intervention')->after('technicien_id');
            $table->time('heure_debut')->nullable()->after('date_intervention');
            $table->time('heure_fin')->nullable()->after('heure_debut');
            $table->unique(['intervention_id', 'technicien_id']);
        });
    }

    public function down(): void
    {
        Schema::table('intervention_technicien', function (Blueprint $table) {
            $table->dropConstrainedForeignId('technicien_id');
            $table->dropConstrainedForeignId('intervention_id');
            $table->dropColumn(['date_intervention', 'heure_debut', 'heure_fin']);
        });
    }
};