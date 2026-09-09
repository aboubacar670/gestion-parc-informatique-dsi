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
        Schema::create('localites', function (Blueprint $table) {
            $table->id();
            $table->string('code_localite');
            $table->foreignId('parent_id')->nullable()->constrained('localites')->onDelete('cascade');
            $table->string('nom');
            $table->foreignId('type_localite_id')->constrained('type_localites');
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localites');
    }
};
