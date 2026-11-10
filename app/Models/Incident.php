<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'code',
        'libelle',
        'structure_id',
        'statut_incident_id',
        'description',
        'date_intervention',
    ];

    // ─── Relations ─────────────────────────────────────────────────────────────

    /** Un incident est signalé depuis une structure */
    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    /** Un incident a un statut (Signalé, En cours, Résolu…) */
    public function statutIncident()
    {
        return $this->belongsTo(StatutIncident::class);
    }

    /**
     * Un incident peut concerner plusieurs équipements (Many-to-Many).
     * Table pivot : equipement_incident
     * Le pivot contient aussi etat_equipement_id (état de l'équipement au moment de l'incident).
     */
    public function equipements()
    {
        return $this->belongsToMany(Equipement::class, 'equipement_incident')
                    ->withPivot('etat_equipement_id')
                    ->withTimestamps();
    }

    /** Les techniciens assignés à cet incident */
    public function techniciens()
    {
        return $this->belongsToMany(Technicien::class, 'incident_technicien')->withTimestamps();
    }
}
