<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('libelle');
            $table->foreignId('type_intervention_id')->constrained('type_interventions');
            $table->foreignId('statut_intervention_id')->constrained('statut_interventions');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->foreignId('structure_id')->constrained('structures');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
