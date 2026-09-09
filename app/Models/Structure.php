<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    protected $fillable = [
        'nom',
        'acronyme',
        'type_structure_id',
        'localite_id',
        'parent_id',
        'longitude',
        'latitude',
    ];

    // ─── Relations récursives ──────────────────────────────────────────────────

    /** Le parent de cette structure (ex : direction provinciale) */
    public function parent()
    {
        return $this->belongsTo(Structure::class, 'parent_id');
    }

    /** Les structures enfants rattachées à celle-ci */
    public function enfants()
    {
        return $this->hasMany(Structure::class, 'parent_id');
    }

    // ─── Relations directes ────────────────────────────────────────────────────

    /** Une structure appartient à un type (école primaire, lycée…) */
    public function typeStructure()
    {
        return $this->belongsTo(TypeStructure::class);
    }

    /** Une structure se situe dans une localité */
    public function localite()
    {
        return $this->belongsTo(Localite::class);
    }

    /** Une structure possède plusieurs équipements */
    public function equipements()
    {
        return $this->hasMany(Equipement::class);
    }

    /** Une structure peut avoir plusieurs incidents signalés */
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    /** Une structure peut faire l'objet de plusieurs interventions */
    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    /** Les utilisateurs (responsables) rattachés à cette structure */
    public function utilisateurs()
    {
        return $this->hasMany(User::class);
    }
}