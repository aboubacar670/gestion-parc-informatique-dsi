<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technicien extends Model
{
  protected $fillable = ['nom', 'prenom', 'telephone', 'profil_technicien_id'];

public function profil()
{
    return $this->belongsTo(ProfilTechnicien::class, 'profil_technicien_id');
}

public function incidents()
{
    return $this->belongsToMany(Incident::class, 'incident_technicien')->withTimestamps();
}

public function interventions()
{
    return $this->belongsToMany(Intervention::class, 'intervention_technicien')
                ->withPivot('date_intervention', 'heure_debut', 'heure_fin')
                ->withTimestamps();
}
}
