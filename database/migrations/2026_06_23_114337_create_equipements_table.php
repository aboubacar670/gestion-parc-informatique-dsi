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
        Schema::create('equipements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('version');
            $table->text('caracteristique');
            $table->foreignId('etat_equipement_id')->constrained('etat_equipements');
            $table->foreignId('type_equipement_id')->constrained('type_equipements');
            $table->foreignId('structure_id')->constrained('structures');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipements');
    }
};
