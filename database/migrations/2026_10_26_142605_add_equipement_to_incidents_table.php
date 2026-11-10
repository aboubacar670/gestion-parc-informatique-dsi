<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('incidents', function (Blueprint $table) {
        $table->foreignId('equipement_id')->nullable()->after('structure_id')
              ->constrained('equipements')->nullOnDelete();
        $table->foreignId('etat_equipement_id')->nullable()->after('equipement_id')
              ->constrained('etat_equipements')->nullOnDelete();
    });
}

   public function down(): void
{
    Schema::table('incidents', function (Blueprint $table) {
        $table->dropConstrainedForeignId('etat_equipement_id');
        $table->dropConstrainedForeignId('equipement_id');
    });
}
};
