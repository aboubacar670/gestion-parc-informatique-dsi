<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table pivot entre Equipement et Incident.
 * Un incident peut concerner plusieurs équipements et vice-versa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipement_incident', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')->constrained('equipements')->onDelete('cascade');
            $table->foreignId('incident_id')->constrained('incidents')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipement_incident');
    }
};
