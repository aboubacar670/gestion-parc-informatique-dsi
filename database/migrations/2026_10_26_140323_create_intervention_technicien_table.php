<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
       Schema::create('intervention_technicien', function (Blueprint $table) {
    $table->id();
    $table->foreignId('intervention_id')->constrained()->cascadeOnDelete();
    $table->foreignId('technicien_id')->constrained('techniciens');
    $table->date('date_intervention');
    $table->time('heure_debut')->nullable();
    $table->time('heure_fin')->nullable();
    $table->timestamps();
    $table->unique(['intervention_id', 'technicien_id']);
});
    }

    
    public function down(): void
    {
        Schema::dropIfExists('intervention_technicien');
    }
};
