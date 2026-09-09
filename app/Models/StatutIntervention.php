<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutIntervention extends Model
{
    protected $fillable = ['libelle'];

    /**
     * Un statut peut s'appliquer à plusieurs interventions.
     */
    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}
