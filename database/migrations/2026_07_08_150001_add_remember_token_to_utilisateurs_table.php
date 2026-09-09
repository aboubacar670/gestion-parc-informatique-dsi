<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajout du champ remember_token à la table utilisateurs.
 * Requis par Laravel Auth pour la fonctionnalité "Se souvenir de moi".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->rememberToken()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });
    }
};
