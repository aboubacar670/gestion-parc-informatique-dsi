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

    /** L'équipement a un état courant */
    public function etatEquipement()
    {
        return $this->belongsTo(EtatEquipement::class);
    }

    /**
     * Un équipement peut être impliqué dans plusieurs incidents (Many-to-Many).
     * Table pivot : equipement_incident
     */
    public function incidents()
    {
        return $this->belongsToMany(Incident::class, 'equipement_incident');
    }
}