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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('libelle');
            $table->foreignId('structure_id')->constrained('structures');
            $table->foreignId('statut_incident_id')->constrained('statut_incidents');
            $table->text('description')->nullable();
            $table->date('date_intervention');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
