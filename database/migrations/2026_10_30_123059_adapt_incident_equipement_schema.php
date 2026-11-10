<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Correction du schéma Incident ↔ Équipement.
 *
 * Problème : la table `incidents` avait à la fois une colonne directe
 * `equipement_id` (one-to-many) ET la table pivot `equipement_incident`
 * (many-to-many). C'est incohérent.
 *
 * Solution :
 * 1. Supprimer les colonnes directes de `incidents` (equipement_id, etat_equipement_id).
 * 2. Ajouter `etat_equipement_id` dans la table pivot `equipement_incident`
 *    pour que chaque équipement d'un incident ait son propre état.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─── 1. Nettoyer la table incidents ────────────────────────────────────
        Schema::table('incidents', function (Blueprint $table) {
            // Vérifier et supprimer les clés étrangères et colonnes si elles existent
            if (Schema::hasColumn('incidents', 'etat_equipement_id')) {
                $table->dropConstrainedForeignId('etat_equipement_id');
            }
            if (Schema::hasColumn('incidents', 'equipement_id')) {
                $table->dropConstrainedForeignId('equipement_id');
            }
        });

        // ─── 2. Enrichir la table pivot equipement_incident ────────────────────
        Schema::table('equipement_incident', function (Blueprint $table) {
            if (!Schema::hasColumn('equipement_incident', 'etat_equipement_id')) {
                $table->foreignId('etat_equipement_id')
                      ->nullable()
                      ->after('incident_id')
                      ->constrained('etat_equipements')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        // Remettre les colonnes directes dans incidents
        Schema::table('incidents', function (Blueprint $table) {
            $table->foreignId('equipement_id')
                  ->nullable()->after('structure_id')
                  ->constrained('equipements')->nullOnDelete();
            $table->foreignId('etat_equipement_id')
                  ->nullable()->after('equipement_id')
                  ->constrained('etat_equipements')->nullOnDelete();
        });

        // Retirer etat_equipement_id du pivot
        Schema::table('equipement_incident', function (Blueprint $table) {
            if (Schema::hasColumn('equipement_incident', 'etat_equipement_id')) {
                $table->dropConstrainedForeignId('etat_equipement_id');
            }
        });
    }
};
