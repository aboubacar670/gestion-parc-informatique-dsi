<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    protected $fillable = [
        'code',
        'libelle',
        'type_intervention_id',
        'statut_intervention_id',   // Correction : double underscore -> simple
        'date_debut',
        'date_fin',
        'structure_id',
    ];

    // ─── Relations ─────────────────────────────────────────────────────────────

    /** Une intervention concerne une structure */
    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    /** Une intervention a un type (Maintenance, Réparation…) */
    public function typeIntervention()
    {
        return $this->belongsTo(TypeIntervention::class);
    }

    /** Une intervention a un statut (Planifiée, En cours, Terminée…) */
    public function statutIntervention()
    {
        return $this->belongsTo(StatutIntervention::class);
    }
}