<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
       Schema::create('techniciens', function (Blueprint $table) {
    $table->id();
    $table->string('nom');
    $table->string('prenom');
    $table->string('telephone')->nullable();
    $table->foreignId('profil_technicien_id')->constrained('profil_techniciens');
    $table->timestamps();
});
    }

    
    public function down(): void
    {
        Schema::dropIfExists('techniciens');
    }
};
