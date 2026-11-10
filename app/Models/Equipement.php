<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipement extends Model
{
    protected $fillable = [
        'nom',
        'version',
        'caracteristique',
        'type_equipement_id',
        'etat_equipement_id',
        'structure_id',
        'description',
    ];

    // ─── Relations ─────────────────────────────────────────────────────────────

    /** L'équipement appartient à une structure */
    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    /** L'équipement appartient à un type */
    public function typeEquipement()
    {
        return $this->belongsTo(TypeEquipement::class);
    }

    /** L'équipement a un état courant (état général de l'équipement) */
    public function etatEquipement()
    {
        return $this->belongsTo(EtatEquipement::class);
    }

    /**
     * Un équipement peut être concerné par plusieurs incidents (Many-to-Many).
     * Le pivot contient etat_equipement_id (état au moment de l'incident).
     */
    public function incidents()
    {
        return $this->belongsToMany(Incident::class, 'equipement_incident')
                    ->withPivot('etat_equipement_id')
                    ->withTimestamps();
    }
}